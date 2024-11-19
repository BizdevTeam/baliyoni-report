// components/Navbar.jsx
import React from "react";
// import ReactDOM from "react-dom";
import { AiOutlineMenu } from "react-icons/ai";
import { CiSearch } from "react-icons/ci";
import { BsPersonCircle } from "react-icons/bs";

const Navbar = ({ open, setOpen }) => {
    return (
        <div className="bg-gradient-to-r from-gray-900 to-gray-800 p-4 shadow-md flex items-center justify-between text-white">
            {/* Sidebar Toggle Icon */}
            <div onClick={() => setOpen(!open)} className="cursor-pointer text-2xl">
                <AiOutlineMenu />
            </div>

            {/* Search Bar */}
            <div className="flex items-center bg-gray-200 px-3 py-1 rounded-md ml-auto w-1/3">
                <CiSearch className="text-gray-500" />
                <input
                    type="search"
                    placeholder="Search"
                    className="bg-transparent focus:outline-none text-white ml-2 w-full"
                />
            </div>

            {/* Profile Info */}
            <div className="flex items-center space-x-3 ml-6">
                <BsPersonCircle className="text-3xl" />
                <span className="font-medium">SuperAdmin</span>
            </div>
        </div>
    );
};

// ReactDOM.render(
//     <Navbar open={true} setOpen={() => {}} />,
//     document.getElementById("navadmin") // Pastikan elemen target sesuai
// );
export default Navbar;