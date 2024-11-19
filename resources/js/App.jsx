import React, { useState } from "react";
import ReactDOM from "react-dom";
import { MdDashboard } from "react-icons/md";
import { BsArrowLeft, BsPersonCircle } from "react-icons/bs";
import { CiSearch } from "react-icons/ci";
import { FaChevronDown } from "react-icons/fa";
import { IoMenu } from "react-icons/io5";
import { AiFillBank, AiOutlineMenu } from "react-icons/ai";

    function App() {
        const [open, setOpen] = useState(true);
        const [openSubmenuIndex, setOpenSubmenuIndex] = useState(null);

        const Menus = [
            {
                title: "Homepage",
                icon: <FaHome />,
            },
            {
                title: "Marketing",
                spacing: true,
                icon: <FaChartBar />,
                submenu: true,
                submenuItems: [
                    { title: "Rekap Penjualan", icon: <img src="icon/RekapPenjualan.svg" alt="Rekap Penjualan" className="w-6 h-6"/> },
                    { title: "Rekap Penjualan Perusahaan", icon: <img src="icon/RekapPenjualanPerusahaan.svg" className="w-6 h-6" /> },
                    { title: "Laporan Paket Administrasi", icon: <img src="icon/LaporanPaketAdministrasi.svg" className="w-6 h-6" /> },
                    { title: "Laporan Status Paket", icon: <img src="icon/LaporanStatusPaket.svg" className="w-6 h-6" /> },
                    { title: "Laporan Per-Instansi", icon: <img src="icon/LaporanPerinstansi.svg" className="w-6 h-6" /> },
                ],
            },
            {
                title: "Procurement",
                icon: <FaShoppingCart />,
                submenu: true,
                submenuItems: [
                    { title: "Laporan Pembelian (Holding)", icon: <img src="icon/laporanpembelian.svg" className="w-6 h-6" /> },
                    { title: "Laporan Stok", icon: <img src="icon/laporanstok.svg" className="w-6 h-6" /> },
                    { title: "Laporan Pembelian Outlet", icon: <img src="icon/laporanpembelianoutlet.svg"  className="w-6 h-6" /> },
                    { title: "Laporan Negosiasi", icon: <img src="icon/laporannegosiasi.svg"  className="w-6 h-6" /> },
                ],
            },
            {
                title: "Support",
                icon: <img src="images/support.svg" className="w-7 h-7"/>, // Ganti ikon di sini
                submenu: true,
                submenuItems: [
                    { title: "Pendapatan Servis ASP", icon: <img src="icon/pendapatanservisasp.svg" className="w-6 h-6" /> },
                    { title: "Piutang Servis ASP", icon: <img src="icon/piutangservisasp.svg" className="w-6 h-6" /> },
                    { title: "Pengiriman Samitra", icon: <img src="icon/penggirimansamitra.svg" className="w-6 h-6" /> },
                    { title: "Pengiriman Detran", icon: <img src="icon/penggirimandetran.svg" className="w-6 h-6" /> },
                ],
            },
            {
                title: "IT",
                icon: <FaTools />,
                submenu: true,
                submenuItems: [
                    { title: "Laporan Multimedia IG", icon: <img src="icon/laporanmultimediaig.svg" className="w-6 h-6" /> },
                    { title: "Laporan Multimedia Tiktok", icon: <img src="icon/laporanmultimediatiktok.svg" className="w-6 h-6" /> },
                    { title: "Laporan Bizdev", icon: <img src="icon/laporanbizdev.svg" className="w-6 h-6" /> },
                ],
            },
            {
                title: "Accounting",
                icon: <FaFileInvoiceDollar />,
                submenu: true,
                submenuItems: [
                    { title: "Laporan Laba Rugi", icon: <img src="icon/labarugi.svg" className="w-6 h-6" /> },
                    { title: "Laporan Neraca", icon: <img src="icon/neraca.svg" className="w-6 h-6" /> },
                    { title: "Laporan Rasio", icon: <img src="icon/rasio.svg" className="w-6 h-6" /> },
                    { title: "Laporan Kas, Hutang, Piutang", icon: <img src="icon/kashutangpiutang.svg" className="w-6 h-6" /> },
                    { title: "Laporan Arus Kas", icon: <img src="icon/aruskas.svg" className="w-6 h-6" /> },
                    { title: "Rekapan PPn Lebih Bayar", icon: <img src="icon/ppnlebihbayar.svg" className="w-6 h-6" /> },
                    { title: "Tax Planning vs Penjualan", icon: <img src="icon/taxplanning.svg" className="w-6 h-6" /> },
                ],
            },
            {
                title: "HRGA",
                icon: <FaUsers />,
                submenu: true,
                submenuItems: [
                    { title: "PT.BOS", icon: <img src="icon/ptbos.svg" className="w-6 h-6" /> },
                    { title: "iJASA", icon: <img src="icon/ijasa.svg" className="w-6 h-6" /> },
                    { title: "Laporan Sakit", icon: <img src="icon/laporansakit.svg" className="w-6 h-6" /> },
                    { title: "Laporan Izin", icon: <img src="icon/laporanizin.svg" className="w-6 h-6" /> },
                    { title: "Laporan Cuti", icon: <img src="icon/laporancuti.svg" className="w-6 h-6" /> },
                    { title: "Laporan Terlambat", icon: <img src="icon/laporanterlambat.svg" className="w-6 h-6" /> },
                ],
            },
            {
                title: "SPI",
                icon: <FaShieldAlt />,
                submenu: true,
                submenuItems: [
                    { title: "Laporan SPI", icon: <img src="icon/laporanspi.svg" className="w-6 h-6" /> },
                    { title: "Laporan SPI-IT", icon: <img src="icon/laporanspiit.svg" className="w-6 h-6" /> },
                ],
            },
            {
                title: "Logout",
                icon: <FaSignOutAlt />,
            },
        ];

        const handleSubmenuClick = (index) => {
            // Toggle submenu: close if it's already open, open if not
            setOpenSubmenuIndex(openSubmenuIndex === index ? null : index);
        };

    return (
        <div className="flex">
            <div
                className={`${
                    open ? "w-72" : "w-20"
                } bg-white h-screen p-5 pt-8 relative duration-300 transition-all`}
            >
                
                
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
                                <span className={`text-2xl block float-left  transition-transform duration-[900ms] ${
                            open ? "rotate-[360deg]" : ""
                        }`}>
                                    <MdDashboard />
                                </span>
                                <span
                                    className={`font-medium text-base flex-1  ${
                                        !open ? "hidden" : ""
                                    } transition-transform duration-300 `}
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
            <div className="flex-1">
                {/* Navbar */}
                <div className="bg-white p-4 shadow-md flex items-center justify-between">
                    {/* Sidebar Toggle Icon */}
                    <div onClick={() => setOpen(!open)} className={`cursor-pointer transition-transform duration-500 text-2xl text-gray-600 ${
                        !open ? "rotate-180" : ""
                    } `}>
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
                    <h1 className="text-2xl font-semibold">Home Page Contentt</h1>
                </div>
            </div>
        </div>
    );
}

    const root = document.getElementById("sidebar");
    if (root) {
        ReactDOM.createRoot(root).render(<App />);
    }
