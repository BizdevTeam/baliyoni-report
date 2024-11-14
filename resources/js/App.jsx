import React, { useState } from "react";
import ReactDOM from "react-dom";
import { MdDashboard } from "react-icons/md";
import { BsArrowLeft } from "react-icons/bs";
import { CiSearch } from "react-icons/ci";
import { FaChevronDown } from "react-icons/fa";
import { IoMenu } from "react-icons/io5";

import { AiFillBank } from "react-icons/ai";

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
                    open ? "w-72" : "w-20"
                } bg-white h-screen p-5 pt-8 relative duration-300 transition-all`}
            >
                <IoMenu 
                    onClick={() => setOpen(!open)}
                    className={`bg-white text-black text-3xl rounded-full absolute -right-3 top-9 border cursor-pointer transition-transform duration-500 ${
                        !open ? "rotate-180" : ""
                    }`}
                />
                <div className="inline-flex">
                    <img    
                        src="http://localhost:8000/images/BYS_LOGO.png"
                        alt="Baliyoni"
                        className={` w-10 h-auto rounded cursor-pointer block float-left mr-2 transition-transform duration-700 ${
                            open ? "rotate-[360deg]" : ""
                        }`}
                    />
              
                    <h1
                        className={`text-black origin-left font-medium text-2xl transition-transform duration-300 ${
                            !open ? "scale-0" : ""
                        }`}
                    >
                        Bal<span className="text-black">i</span>yoni
                    </h1>
                </div>

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
                    <input
                        type="search"
                        placeholder="search"
                        className={`text-base bg-transparent w-full text-black focus:outline-none ${
                            !open ? "hidden" : ""
                        }`}
                    />
                </div>
                <ul className="pt-2">
                    {Menus.map((menu, index) => (
                        <React.Fragment key={index}>
                            <li
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
                                </ul>
                            )}
                        </React.Fragment>
                    ))}
                </ul>
            </div>
            <div className="p-7 bg-slate-200 w-full">
                <h1 className="text-2xl font-semibold">Home Page</h1>
            </div>
        </div>
    );
}

// Render the component
if (document.getElementById("sidebar")) {
    ReactDOM.render(<App />, document.getElementById("sidebar"));
}
