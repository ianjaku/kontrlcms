import "kontrl-sidebars/style/style.scss";
import sidebars, { SidebarSet, Sidebar, TitleItem, FormItem, InputItem } from "kontrl-sidebars";

const sidebarSet = new SidebarSet();

sidebarSet.addToDOM();

export default {
    sidebars,
    addSidebar: sidebarSet.addSidebar.bind(sidebarSet),
    addSidebars: sidebarSet.addSidebars.bind(sidebarSet),
}
