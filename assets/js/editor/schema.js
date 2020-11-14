import {Schema} from "prosemirror-model";

export let nodes = {
    doc: {
        content: "block+"
    },
    paragraph: {
        content: "inline*",
        group: "block",
        parseDOM: [{tag: "p"}],
        toDOM() { return ["p", 0] }
    },
    heading: {
        attrs: {level: {default: 1}},
        content: "inline*",
        group: "block",
        defining: true,
        parseDOM: [
            {tag: "h1", attrs: {level: 1}},
            {tag: "h2", attrs: {level: 2}},
            // {tag: "h3", attrs: {level: 3}},
        ],
        toDOM(node) { return ["h" + node.attrs.level, 0] }
    },
    text: {
        group: "inline"
    }
};

export let marks = {
    link: {
        attrs: {
            href: {},
            title: {default: null}
        },
        inclusive: false,
        parseDOM: [{tag: "a[href]", getAttrs(dom) {
                return {href: dom.getAttribute("href"), title: dom.getAttribute("title")}
            }}],
        toDOM(node) { let {href, title} = node.attrs; return ["a", {href, title}, 0] }
    },
    em: {
        parseDOM: [{tag: "i"}, {tag: "em"}, {style: "font-style=italic"}],
        toDOM() { return emDOM }
    },
    strong: {
        parseDOM: [
            {tag: "strong"},
            // This works around a Google Docs misbehavior where
            // pasted content will be inexplicably wrapped in `<b>`
            // tags with a font-weight normal.
            {tag: "b", getAttrs: node => node.style.fontWeight != "normal" && null},
            {style: "font-weight", getAttrs: value => /^(bold(er)?|[5-9]\d{2,})$/.test(value) && null}],
        toDOM() { return ["strong", 0] }
    }
};

export default new Schema({nodes, marks});
