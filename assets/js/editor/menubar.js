import {wrapItem, blockTypeItem, Dropdown, DropdownSubmenu, joinUpItem, liftItem, undoItem, redoItem, icons, MenuItem} from "prosemirror-menu"
import {NodeSelection} from "prosemirror-state"
import {toggleMark} from "prosemirror-commands"
import {wrapInList} from "prosemirror-schema-list"
import {TextField, openPrompt} from "./prompt"
import imagePopup from "../popups/image_popup";
import {uploadAnyImage} from "../util/uploader";
import simplePopup from "../popups/simple_popup";

// import "prosemirror-menu/style/menu.css";

// Helpers to create specific types of items

function canInsert(state, nodeType) {
    let $from = state.selection.$from
    for (let d = $from.depth; d >= 0; d--) {
        let index = $from.index(d)
        if ($from.node(d).canReplaceWith(index, index, nodeType)) return true
    }
    return false
}

function insertImageItem(nodeType) {
    return new MenuItem({
        title: "Insert image",
        // icon: '<svg aria-hidden="true" width="10px" heihgt="10px" focusable="false" data-prefix="fas" data-icon="images" class="svg-inline--fa fa-images fa-w-18" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512"><path fill="currentColor" d="M480 416v16c0 26.51-21.49 48-48 48H48c-26.51 0-48-21.49-48-48V176c0-26.51 21.49-48 48-48h16v208c0 44.112 35.888 80 80 80h336zm96-80V80c0-26.51-21.49-48-48-48H144c-26.51 0-48 21.49-48 48v256c0 26.51 21.49 48 48 48h384c26.51 0 48-21.49 48-48zM256 128c0 26.51-21.49 48-48 48s-48-21.49-48-48 21.49-48 48-48 48 21.49 48 48zm-96 144l55.515-55.515c4.686-4.686 12.284-4.686 16.971 0L272 256l135.515-135.515c4.686-4.686 12.284-4.686 16.971 0L512 208v112H160v-48z"></path></svg>',
        icon: {
            path: 'M480 416v16c0 26.51-21.49 48-48 48H48c-26.51 0-48-21.49-48-48V176c0-26.51 21.49-48 48-48h16v208c0 44.112 35.888 80 80 80h336zm96-80V80c0-26.51-21.49-48-48-48H144c-26.51 0-48 21.49-48 48v256c0 26.51 21.49 48 48 48h384c26.51 0 48-21.49 48-48zM256 128c0 26.51-21.49 48-48 48s-48-21.49-48-48 21.49-48 48-48 48 21.49 48 48zm-96 144l55.515-55.515c4.686-4.686 12.284-4.686 16.971 0L272 256l135.515-135.515c4.686-4.686 12.284-4.686 16.971 0L512 208v112H160v-48z',
            width: 576,
            height: 512,
        },
        label: "Image",
        enable(state) { return canInsert(state, nodeType) },
        run(state, _, view) {
            let {from, to} = state.selection, attrs = null
            if (state.selection instanceof NodeSelection && state.selection.node.type == nodeType)
                attrs = state.selection.node.attrs
            // openPrompt({
            //     title: "Insert image",
            //     fields: {
            //         src: new TextField({label: "Location", required: true, value: attrs && attrs.src}),
            //         title: new TextField({label: "Title", value: attrs && attrs.title}),
            //         alt: new TextField({label: "Description",
            //             value: attrs ? attrs.alt : state.doc.textBetween(from, to, " ")})
            //     },
            //     callback(attrs) {
            //         view.dispatch(view.state.tr.replaceSelectionWith(nodeType.createAndFill(attrs)))
            //         view.focus()
            //     }
            // })
            imagePopup((imgFile) => {
                uploadAnyImage(imgFile, "wysiwyg", (url) => {
                    const attrs = { src: url, alt: "" }
                    view.dispatch(view.state.tr.replaceSelectionWith(nodeType.createAndFill(attrs)));
                });
            });
        }
    })
}

function cmdItem(cmd, options) {
    let passedOptions = {
        label: options.title,
        run: cmd
    }
    for (let prop in options) passedOptions[prop] = options[prop]
    if ((!options.enable || options.enable === true) && !options.select)
        passedOptions[options.enable ? "enable" : "select"] = state => cmd(state)

    return new MenuItem(passedOptions)
}

function markActive(state, type) {
    let {from, $from, to, empty} = state.selection
    if (empty) return type.isInSet(state.storedMarks || $from.marks())
    else return state.doc.rangeHasMark(from, to, type)
}

function markItem(markType, options) {
    let passedOptions = {
        active(state) { return markActive(state, markType) },
        enable: true
    }
    for (let prop in options) passedOptions[prop] = options[prop]
    return cmdItem(toggleMark(markType), passedOptions)
}

function linkItem(markType) {
    return new MenuItem({
        title: "Add or remove link",
        icon: {
            width: 512,
            height: 512,
            path: "M326.612 185.391c59.747 59.809 58.927 155.698.36 214.59-.11.12-.24.25-.36.37l-67.2 67.2c-59.27 59.27-155.699 59.262-214.96 0-59.27-59.26-59.27-155.7 0-214.96l37.106-37.106c9.84-9.84 26.786-3.3 27.294 10.606.648 17.722 3.826 35.527 9.69 52.721 1.986 5.822.567 12.262-3.783 16.612l-13.087 13.087c-28.026 28.026-28.905 73.66-1.155 101.96 28.024 28.579 74.086 28.749 102.325.51l67.2-67.19c28.191-28.191 28.073-73.757 0-101.83-3.701-3.694-7.429-6.564-10.341-8.569a16.037 16.037 0 0 1-6.947-12.606c-.396-10.567 3.348-21.456 11.698-29.806l21.054-21.055c5.521-5.521 14.182-6.199 20.584-1.731a152.482 152.482 0 0 1 20.522 17.197zM467.547 44.449c-59.261-59.262-155.69-59.27-214.96 0l-67.2 67.2c-.12.12-.25.25-.36.37-58.566 58.892-59.387 154.781.36 214.59a152.454 152.454 0 0 0 20.521 17.196c6.402 4.468 15.064 3.789 20.584-1.731l21.054-21.055c8.35-8.35 12.094-19.239 11.698-29.806a16.037 16.037 0 0 0-6.947-12.606c-2.912-2.005-6.64-4.875-10.341-8.569-28.073-28.073-28.191-73.639 0-101.83l67.2-67.19c28.239-28.239 74.3-28.069 102.325.51 27.75 28.3 26.872 73.934-1.155 101.96l-13.087 13.087c-4.35 4.35-5.769 10.79-3.783 16.612 5.864 17.194 9.042 34.999 9.69 52.721.509 13.906 17.454 20.446 27.294 10.606l37.106-37.106c59.271-59.259 59.271-155.699.001-214.959z"
        },
        active(state) { return markActive(state, markType) },
        enable(state) { return !state.selection.empty },
        run(state, dispatch, view) {
            if (markActive(state, markType)) {
                toggleMark(markType)(state, dispatch)
                return true
            }
            // openPrompt({
            //     title: "Create a link",
            //     fields: {
            //         href: new TextField({
            //             label: "Link target",
            //             required: true
            //         }),
            //         title: new TextField({label: "Title"})
            //     },
            //     callback(attrs) {
            //         toggleMark(markType, attrs)(view.state, view.dispatch)
            //         view.focus()
            //     }
            // })
            simplePopup(
                "Create link",
                "Where would you like the link to redirect to",
                [
                    {type: "text", label: "Link location", name: "link"}
                ],
                ({link}) => {
                    if (link === "") return;
                    toggleMark(markType, { href: link, title: "" })(view.state, view.dispatch);
                    view.focus();
                }
            );
        }
    })
}

function wrapListItem(nodeType, options) {
    return cmdItem(wrapInList(nodeType, options.attrs), options)
}

// :: (Schema) → Object
// Given a schema, look for default mark and node types in it and
// return an object with relevant menu items relating to those marks:
//
// **`toggleStrong`**`: MenuItem`
//   : A menu item to toggle the [strong mark](#schema-basic.StrongMark).
//
// **`toggleEm`**`: MenuItem`
//   : A menu item to toggle the [emphasis mark](#schema-basic.EmMark).
//
// **`toggleCode`**`: MenuItem`
//   : A menu item to toggle the [code font mark](#schema-basic.CodeMark).
//
// **`toggleLink`**`: MenuItem`
//   : A menu item to toggle the [link mark](#schema-basic.LinkMark).
//
// **`insertImage`**`: MenuItem`
//   : A menu item to insert an [image](#schema-basic.Image).
//
// **`wrapBulletList`**`: MenuItem`
//   : A menu item to wrap the selection in a [bullet list](#schema-list.BulletList).
//
// **`wrapOrderedList`**`: MenuItem`
//   : A menu item to wrap the selection in an [ordered list](#schema-list.OrderedList).
//
// **`wrapBlockQuote`**`: MenuItem`
//   : A menu item to wrap the selection in a [block quote](#schema-basic.BlockQuote).
//
// **`makeParagraph`**`: MenuItem`
//   : A menu item to set the current textblock to be a normal
//     [paragraph](#schema-basic.Paragraph).
//
// **`makeCodeBlock`**`: MenuItem`
//   : A menu item to set the current textblock to be a
//     [code block](#schema-basic.CodeBlock).
//
// **`makeHead[N]`**`: MenuItem`
//   : Where _N_ is 1 to 6. Menu items to set the current textblock to
//     be a [heading](#schema-basic.Heading) of level _N_.
//
// **`insertHorizontalRule`**`: MenuItem`
//   : A menu item to insert a horizontal rule.
//
// The return value also contains some prefabricated menu elements and
// menus, that you can use instead of composing your own menu from
// scratch:
//
// **`insertMenu`**`: Dropdown`
//   : A dropdown containing the `insertImage` and
//     `insertHorizontalRule` items.
//
// **`typeMenu`**`: Dropdown`
//   : A dropdown containing the items for making the current
//     textblock a paragraph, code block, or heading.
//
// **`fullMenu`**`: [[MenuElement]]`
//   : An array of arrays of menu elements for use as the full menu
//     for, for example the [menu bar](https://github.com/prosemirror/prosemirror-menu#user-content-menubar).
export function buildMenuItems(schema) {
    let r = {}, type
    if (type = schema.marks.strong)
        r.toggleStrong = markItem(type, {
            title: "Toggle strong style",
            icon: {
                width: 384,
                height: 512,
                path: "M333.49 238a122 122 0 0 0 27-65.21C367.87 96.49 308 32 233.42 32H34a16 16 0 0 0-16 16v48a16 16 0 0 0 16 16h31.87v288H34a16 16 0 0 0-16 16v48a16 16 0 0 0 16 16h209.32c70.8 0 134.14-51.75 141-122.4 4.74-48.45-16.39-92.06-50.83-119.6zM145.66 112h87.76a48 48 0 0 1 0 96h-87.76zm87.76 288h-87.76V288h87.76a56 56 0 0 1 0 112z"
            }
        })
    if (type = schema.marks.em)
        r.toggleEm = markItem(type, {
            title: "Toggle emphasis",
            icon: {
                width: 320,
                height: 512,
                path: "M320 48v32a16 16 0 0 1-16 16h-62.76l-80 320H208a16 16 0 0 1 16 16v32a16 16 0 0 1-16 16H16a16 16 0 0 1-16-16v-32a16 16 0 0 1 16-16h62.76l80-320H112a16 16 0 0 1-16-16V48a16 16 0 0 1 16-16h192a16 16 0 0 1 16 16z"
            }
        })
    if (type = schema.marks.code)
        r.toggleCode = markItem(type, {title: "Toggle code font", icon: icons.code})
    if (type = schema.marks.link)
        r.toggleLink = linkItem(type);

    if (type = schema.nodes.image)
        r.insertImage = insertImageItem(type)
    if (type = schema.nodes.bullet_list)
        r.wrapBulletList = wrapListItem(type, {
            title: "Wrap in bullet list",
            icon: icons.bulletList
        })
    if (type = schema.nodes.ordered_list)
        r.wrapOrderedList = wrapListItem(type, {
            title: "Wrap in ordered list",
            icon: icons.orderedList
        })
    if (type = schema.nodes.blockquote)
        r.wrapBlockQuote = wrapItem(type, {
            title: "Wrap in block quote",
            icon: {
                width: 512,
                height: 512,
                path: "M464 256h-80v-64c0-35.3 28.7-64 64-64h8c13.3 0 24-10.7 24-24V56c0-13.3-10.7-24-24-24h-8c-88.4 0-160 71.6-160 160v240c0 26.5 21.5 48 48 48h128c26.5 0 48-21.5 48-48V304c0-26.5-21.5-48-48-48zm-288 0H96v-64c0-35.3 28.7-64 64-64h8c13.3 0 24-10.7 24-24V56c0-13.3-10.7-24-24-24h-8C71.6 32 0 103.6 0 192v240c0 26.5 21.5 48 48 48h128c26.5 0 48-21.5 48-48V304c0-26.5-21.5-48-48-48z"
            }
        })
    if (type = schema.nodes.paragraph)
        r.makeParagraph = blockTypeItem(type, {
            title: "Change to paragraph",
            label: "Text"
        })
    if (type = schema.nodes.code_block)
        r.makeCodeBlock = blockTypeItem(type, {
            title: "Change to code block",
            label: "Code"
        })
    if (type = schema.nodes.heading)
        for (let i = 1; i <= 2; i++)
            r["makeHead" + i] = blockTypeItem(type, {
                title: "Change to heading " + i,
                label: "Heading" + i,
                attrs: {level: i}
            })
    if (type = schema.nodes.horizontal_rule) {
        let hr = type
        r.insertHorizontalRule = new MenuItem({
            title: "Insert horizontal rule",
            label: "Horizontal rule",
            enable(state) { return canInsert(state, hr) },
            run(state, dispatch) { dispatch(state.tr.replaceSelectionWith(hr.create())) }
        })
    }

    let cut = arr => arr.filter(x => x)
    r.insertMenu = new Dropdown(cut([r.insertImage, r.insertHorizontalRule]), {label: "Insert"});
    // r.typeMenu = new Dropdown(cut([r.makeParagraph, r.makeCodeBlock, r.makeHead1 && new DropdownSubmenu(cut([
    //     r.makeHead1, r.makeHead2, r.makeHead3, r.makeHead4, r.makeHead5, r.makeHead6
    // ]), {label: "Heading"})]), {label: "Type..."})
    r.typeMenu = new Dropdown(cut([r.makeHead1, r.makeHead2, r.makeParagraph]), {label: "Text Type"});

    // r.inlineMenu = [cut([r.toggleStrong, r.toggleEm, r.toggleCode, r.toggleLink])];
    // r.blockMenu = [cut([r.wrapBulletList, r.wrapOrderedList, r.wrapBlockQuote, joinUpItem,
    //     liftItem])]
    // r.fullMenu = r.inlineMenu.concat([[r.insertMenu, r.typeMenu]], [[undoItem, redoItem]], r.blockMenu);

    return [
        [r.typeMenu],
        [r.toggleStrong, r.toggleEm, r.toggleLink],
        [r.insertImage],
        [r.wrapBlockQuote, r.wrapBulletList, r.wrapOrderedList]
    ];
}
