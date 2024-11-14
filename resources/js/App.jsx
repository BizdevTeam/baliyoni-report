import React, { useState } from "react";
import ReactDOM from "react-dom";
import { MdDashboard } from "react-icons/md";
import { CiSearch } from "react-icons/ci";
import { FaChevronDown } from "react-icons/fa";
import { AiOutlineMenu } from "react-icons/ai";
import { BsPersonCircle } from "react-icons/bs";

function App() {
    const [open, setOpen] = useState(true);
    const [submenuOpen, setSubmenuOpen] = useState(false);
    const Menus = [
        { title: "Dashboard" },
        { title: "Pages" },
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
        <div className="flex">
            {/* Sidebar */}
            <div
                className={`${open ? "w-72" : "w-20"} bg-red-600 h-screen p-5 pt-8 relative duration-300 transition-all`}
            >
                {/* Sidebar toggle button */}
                <div
                    onClick={() => setOpen(!open)}
                    className={`text-black text-3xl absolute top-9 -right-4 cursor-pointer transition-transform duration-300 ${!open ? "rotate-180" : ""}`}
                >
                    <AiOutlineMenu />
                </div>
                {/* Sidebar Logo */}
                <div className="inline-flex">
                    <img
                        src="/images/BYS_LOGO.png"
                        alt="Baliyoni"
                        className={`w-10 h-auto rounded cursor-pointer transition-transform duration-500 ${open ? "rotate-[360deg]" : ""}`}
                    />
                    <h1 className={`text-black origin-left font-medium text-2xl transition-transform duration-300 ${!open ? "scale-0" : ""}`}>
                        Bal<span className="text-black">i</span>yoni
                    </h1>
                </div>
                {/* Sidebar Search */}
                <div className={`flex items-center rounded-md bg-white mt-6 ${!open ? "px-2.5" : "px-4"} py-2`}>
                    <CiSearch className={`text-black text-lg cursor-pointer ${open ? "mr-2" : ""}`} />
                    <input
                        type="search"
                        placeholder="search"
                        className={`text-base bg-transparent w-full text-black focus:outline-none ${!open ? "hidden" : ""}`}
                    />
                </div>
                {/* Sidebar Menu */}
                <ul className="pt-2">
                    {Menus.map((menu, index) => (
                        <React.Fragment key={index}>
                            <li
                                className={`text-white text-sm flex items-center gap-x-4 cursor-pointer p-2 ${menu.spacing ? "mt-9" : "mt-2"} hover:bg-red-700 rounded-md`}
                            >
                                <span className="text-2xl">
                                    <MdDashboard />
                                </span>
                                <span className={`font-medium text-base flex-1 ${!open ? "hidden" : ""}`}>
                                    {menu.title}
                                </span>
                                {menu.submenu && open && (
                                    <FaChevronDown
                                        onClick={() => setSubmenuOpen(!submenuOpen)}
                                        className={`cursor-pointer transition-transform duration-300 ${submenuOpen ? "rotate-180" : ""}`}
                                    />
                                )}
                            </li>
                            {menu.submenu && submenuOpen && open && (
                                <ul>
                                    {menu.submenuItems.map((submenuItem, subIndex) => (
                                        <li
                                            key={subIndex}
                                            className="text-white text-sm flex items-center gap-x-4 cursor-pointer p-2 hover:bg-red-700 rounded-md px-7"
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

            {/* Main Content with Navbar */}
            <div className="flex-1">
                {/* Navbar */}
                <div className="bg-white p-4 shadow-md flex items-center justify-between">
                    {/* Sidebar Toggle Icon */}
                    <div onClick={() => setOpen(!open)} className="cursor-pointer text-2xl text-gray-600">
                        <AiOutlineMenu />
                    </div>
                    {/* Search Bar */}
                    <div className="flex items-center bg-gray-100 px-3 py-2 rounded-md w-1/3">
                        <CiSearch className="text-gray-500" />
                        <input
                            type="search"
                            placeholder="Search"
                            className="bg-transparent focus:outline-none text-gray-700 ml-2 w-full"
                        />
                    </div>
                    {/* Home Page Title */}
                    <h1 className="text-lg font-semibold text-gray-700">Home Page</h1>
                    {/* Profile and Menu Icons */}
                    <div className="flex items-center space-x-4">
                        <FaChevronDown className="text-gray-600 cursor-pointer" />
                        <BsPersonCircle className="text-3xl text-gray-600 cursor-pointer" />
                    </div>
                </div>

                {/* Content */}
                <div className="p-7">
                    <h1 className="text-2xl font-semibold">Home Page Content</h1>
                </div>
            </div>
        </div>
    );
}

// Render the component
if (document.getElementById("sidebar")) {
    ReactDOM.render(<App />, document.getElementById("sidebar"));
}
