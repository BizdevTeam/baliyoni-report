import React, { useState } from "react";
import ReactDOM from "react-dom";
import { MdDashboard } from "react-icons/md";
import { BsArrowLeft, BsPersonCircle } from "react-icons/bs";
import { CiSearch } from "react-icons/ci";
import { FaChevronDown } from "react-icons/fa";
import { IoMenu } from "react-icons/io5";
import { FaCircle } from "react-icons/fa6";



import { AiFillBank, AiOutlineMenu } from "react-icons/ai";

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
            <div
                className={`${
                    open ? "w-64" : "w-20"
                } bg-white h-screen p-5 pt-8 relative duration-300 transition-all`}
            >
                
                
                <div className="inline-flex items-center">
                    <img
                        src="http://localhost:8000/images/BYS_LOGO.png"
                        alt="Baliyoni"
                        className={`w-10 h-auto rounded cursor-pointer mr-2 transform transition-transform duration-700 ${
                            open ? "rotate-360" : ""
                        } ${
                            open ? "rotate-[360deg]" : ""
                        }`}
                    />
                    <h1
                        className={`font-bold text-xl transition-transform duration-300 ${
                            !open ? "scale-0" : ""
                        }`}
                    >
                        Bal<span className="text-yellow-500">i</span>yoni
                    </h1>
                </div>

                        
              
                <ul className="pt-6">
                    {Menus.map((menu, index) => (
                        <React.Fragment key={index}>
                            <li
                                className={`flex items-center gap-x-4 p-2 cursor-pointer rounded-lg  hover:bg-red-600 hover:text-white ${
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
                                        onClick={() =>
                                            setSubmenuOpen(!submenuOpen)
                                        }
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
                                </ul>
                            )}
                        </React.Fragment>
                    ))}
                </ul>
            </div>
             {/* Main Content */}
             <div className="flex-1">
                {/* Navbar */}
                <div className="bg-gradient-to-r from-gray-900 to-gray-800 p-4 shadow-md flex items-center justify-between text-white">
                    {/* Sidebar Toggle Icon */}
                    <div
                        onClick={() => setOpen(!open)}
                        className="cursor-pointer text-2xl"
                    >
                        <AiOutlineMenu />
                    </div>

                    {/* Search Bar */}
                    <div className="flex items-center bg-gray-200 px-3 py-1 rounded-full ml-auto w-1/4">
                        <CiSearch className="text-gray-500" />
                        <input
                            type="search"
                            placeholder="Search"
                            className="bg-transparent focus:outline-none text-white ml-2 w-full"
                        />
                    </div>

                    {/* Profile Info */}
                    <div className="flex items-center space-x-3 ml-4">
                        <FaCircle className="text-red-500" />
                        <span className="font-medium">SuperAdmin</span>
                        <BsPersonCircle className="text-3xl" />
                    </div>
                </div>

                {/* Page Content */}
                <div className="p-6 bg-gray-100 min-h-screen">
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
