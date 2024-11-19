import React, { useState } from "react";
// import ReactDOM from "react-dom";
import { MdDashboard } from "react-icons/md";
import { FaChevronDown } from "react-icons/fa";

const Sidebar = ({ open, setOpen }) => {
    const [submenuOpen, setSubmenuOpen] = useState(false);
    const Menus = [
        { title: "Homepage" },
        { title: "Dashboard" },
        { title: "Media", spacing: true },
        {
            title: "Projects",
            submenu: true,
            submenuItems: [
                { title: "submenu 1" },
                { title: "submenu 2" },
                { title: "submenu 3" },
            ],
        },
        { title: "Analytics" },
        { title: "Inbox" },
        { title: "Profile" },
        { title: "Setting" },
        { title: "Logout" },
    ];

    return (
        <div
            className={`${
                open ? "w-64" : "w-20"
            } bg-white h-screen p-5 pt-8 relative duration-300 transition-all`}
        >
            {/* Logo Section */}
            <div className={`inline-flex ${open ? "justify-center w-full" : ""}`}>
                {open ? (
                    <img
                        src="/asset/baliyoni.png"
                        alt="Maximized Image"
                        className="w-52 h-auto rounded cursor-pointer block float-left mr-2 transition-transform duration-700"
                    />
                ) : (
                    <img
                        src="images/BYS_LOGO.png"
                        alt="Baliyoni Logo"
                        className="w-10 h-auto rounded cursor-pointer block float-left mr-2 transition-transform duration-700"
                    />
                )}
            </div>

            {/* Menu Items */}
            <ul className="pt-6">
                {Menus.map((menu, index) => (
                    <React.Fragment key={index}>
                        <li
                            className={`flex items-center gap-x-4 p-2 cursor-pointer rounded-lg hover:bg-red-600 hover:text-white ${
                                menu.spacing && open ? "mt-6" : "mt-2"
                            }`}
                        >
                            <MdDashboard className="text-xl" />
                            <span
                                className={`font-medium flex-1 transition-all duration-300 ${
                                    !open ? "hidden" : ""
                                }`}
                            >
                                {menu.title}
                            </span>
                            {menu.submenu && open && (
                                <FaChevronDown
                                    onClick={() => setSubmenuOpen(!submenuOpen)}
                                    className={`cursor-pointer transition-transform duration-300 ${
                                        submenuOpen ? "rotate-180" : ""
                                    }`}
                                />
                            )}
                        </li>
                        {menu.submenu && (
                            <ul
                                className={`overflow-hidden transition-all duration-[900ms] ease-in-out ${
                                    submenuOpen ? "max-h-40" : "max-h-0"
                                }`}
                            >
                                {menu.submenuItems.map((submenuItem, subIndex) => (
                                    <li
                                        key={subIndex}
                                        className="text-black hover:text-white text-sm flex items-center gap-x-4 cursor-pointer p-2 hover:bg-red-600 rounded-md px-7"
                                    >
                                        {submenuItem.title}
                                    </li>
                                ))}
                            </ul>
                        )}
                    </React.Fragment>
                ))}
            </ul>
        </div>
    );
};

// ReactDOM.render(
//     <Sidebar open={true} setOpen={() => {}} />,
//     document.getElementById("sideadmin") // Pastikan elemen target sesuai
// );
export default Sidebar;