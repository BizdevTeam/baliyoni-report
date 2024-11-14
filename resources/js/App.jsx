import React, { useState } from "react";
import ReactDOM from "react-dom";
import { MdDashboard } from "react-icons/md";
import { CiSearch } from "react-icons/ci";
import { FaChevronDown } from "react-icons/fa";
<<<<<<< HEAD
import { AiOutlineMenu } from "react-icons/ai";
import { BsPersonCircle } from "react-icons/bs";
=======
import { IoMenu } from "react-icons/io5";

import { AiFillBank } from "react-icons/ai";
>>>>>>> 92f3569c9a27b9e3ba0b1de82dbc6fbe92b31e53

function App() {
    const [open, setOpen] = useState(true);
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
        <div className="flex">
            {/* Sidebar */}
            <div
<<<<<<< HEAD
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
=======
                className={`${
                    open ? "w-72" : "w-20"
                } bg-white h-screen p-5 pt-8 relative duration-300 transition-all`}
            >
                <IoMenu 
                    onClick={() => setOpen(!open)}
                    className={`bg-white text-black text-3xl rounded-full absolute -right-3 top-9 border cursor-pointer transition-transform duration-500 ${
                        !open ? "rotate-180" : ""
                    }`}
                />
>>>>>>> 92f3569c9a27b9e3ba0b1de82dbc6fbe92b31e53
                <div className="inline-flex">
                    <img
                        src="/images/BYS_LOGO.png"
                        alt="Baliyoni"
<<<<<<< HEAD
                        className={`w-10 h-auto rounded cursor-pointer transition-transform duration-500 ${open ? "rotate-[360deg]" : ""}`}
=======
                        className={` w-10 h-auto rounded cursor-pointer block float-left mr-2 transition-transform duration-700 ${
                            open ? "rotate-[360deg]" : ""
                        }`}
>>>>>>> 92f3569c9a27b9e3ba0b1de82dbc6fbe92b31e53
                    />
                    <h1 className={`text-black origin-left font-medium text-2xl transition-transform duration-300 ${!open ? "scale-0" : ""}`}>
                        Bal<span className="text-black">i</span>yoni
                    </h1>
                </div>
<<<<<<< HEAD
                {/* Sidebar Search */}
                <div className={`flex items-center rounded-md bg-white mt-6 ${!open ? "px-2.5" : "px-4"} py-2`}>
                    <CiSearch className={`text-black text-lg cursor-pointer ${open ? "mr-2" : ""}`} />
=======

                <div
                    className={`flex items-center rounded-xl bg-white border mt-6 ${
                        !open ? "px-2.5" : "px-4"
                    } py-2`}
                >
                    <CiSearch
                        className={`text-black text-lg block float-left cursor-pointer ${
                            open ? "mr-2" : ""
                        }`}
                    />
>>>>>>> 92f3569c9a27b9e3ba0b1de82dbc6fbe92b31e53
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
<<<<<<< HEAD
                                className={`text-white text-sm flex items-center gap-x-4 cursor-pointer p-2 ${menu.spacing ? "mt-9" : "mt-2"} hover:bg-red-700 rounded-md`}
                            >
                                <span className="text-2xl">
                                    <MdDashboard />
                                </span>
                                <span className={`font-medium text-base flex-1 ${!open ? "hidden" : ""}`}>
=======
                                className={`text-black text-sm flex items-center gap-x-4 cursor-pointer p-2 ${
                                    menu.spacing && open ?  "mt-6" : "mt-2"
                                }  hover:bg-red-600 hover:text-white rounded-md`}
                            >
                                <span className={`text-2xl block float-left  transition-transform duration-500 ${
                            open ? "rotate-[360deg]" : ""
                        }`}>
                                    <MdDashboard />
                                </span>
                                <span
                                    className={`font-medium text-base flex-1  ${
                                        !open ? "hidden" : ""
                                    } transition-transform duration-300 ` }
                                >
>>>>>>> 92f3569c9a27b9e3ba0b1de82dbc6fbe92b31e53
                                    {menu.title}
                                </span>
                                {menu.submenu && open && (
                                    <FaChevronDown
                                        onClick={() => setSubmenuOpen(!submenuOpen)}
                                        className={`cursor-pointer transition-transform duration-300 ${submenuOpen ? "rotate-180" : ""}`}
                                    />
                                )}
                            </li>
<<<<<<< HEAD
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
=======
                            {menu.submenu && (
                                <ul
                                    className={`overflow-hidden transition-all duration-700 ease-in-out ${
                                        submenuOpen ? "max-h-40" : "max-h-0"
                                    }`}
                                >
                                    {menu.submenuItems.map(
                                        (submenuItem, subIndex) => (
                                            <li
                                                key={subIndex}
                                                className="text-black hover:text-white text-sm flex items-center gap-x-4 cursor-pointer p-2 hover:bg-red-600 rounded-md px-7"
                                            >
                                                {submenuItem.title}
                                            </li>
                                        )
                                    )}
>>>>>>> 92f3569c9a27b9e3ba0b1de82dbc6fbe92b31e53
                                </ul>
                            )}
                        </React.Fragment>
                    ))}
                </ul>
            </div>
<<<<<<< HEAD

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
=======
            <div className="p-7 bg-slate-200 w-full">
                <h1 className="text-2xl font-semibold">Home Page</h1>
>>>>>>> 92f3569c9a27b9e3ba0b1de82dbc6fbe92b31e53
            </div>
        </div>
    );
}

// Render the component
if (document.getElementById("sidebar")) {
    ReactDOM.render(<App />, document.getElementById("sidebar"));
}
