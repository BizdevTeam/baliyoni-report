// resources/js/App.jsx
import React, { useState } from "react";
import ReactDOM from "react-dom";
import Sidebar from "./components/sideadmin";
import Navbar from "./components/navadmin";

function App() {
    const [open, setOpen] = useState(true);

    return (
        <div className="flex">
            {/* Sidebar */}
            <Sidebar open={open} setOpen={setOpen} />

            {/* Main Content */}
            <div className="flex-1">
                {/* Navbar */}
                <Navbar open={open} setOpen={setOpen} />

                {/* Content Section */}
                <div className="p-6 bg-gray-100 min-h-screen">
                    <iframe 
                        src='dashboard/admin'
                        className="w-full h-full border-none" 
                        title="Main Content"
                    />
                </div>
            </div>
        </div>
    );
}

if (document.getElementById("adminApp")) {
    ReactDOM.render(<App />, document.getElementById("adminApp"));
}

