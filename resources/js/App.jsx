    import React, { useState } from "react";
    import ReactDOM from "react-dom/client";
    import { MdDashboard } from "react-icons/md";
    import { BsPersonCircle } from "react-icons/bs";
    import { CiSearch } from "react-icons/ci";
    import { AiOutlineMenu } from "react-icons/ai";
    import { FaHome, FaChartBar, FaShoppingCart, FaUsers, FaShieldAlt, FaSignOutAlt } from 'react-icons/fa';
    import { AiOutlineDollar, AiOutlinePieChart, AiOutlineTeam } from 'react-icons/ai';
    import { MdOutlineReport, MdOutlineInventory } from 'react-icons/md';
    import { FaChevronDown, FaTools, FaFileInvoiceDollar } from 'react-icons/fa';

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
                <div className={`${open ? "w-72" : "w-20"} bg-white h-screen p-5 pt-8 relative duration-300`}>
                    <div className="inline-flex">
                        <img
                            src="/images/BYS_LOGO.png"
                            alt="Baliyoni"
                            className={`w-10 h-auto rounded cursor-pointer block float-left mr-2 transition-transform duration-700 ${open ? "rotate-[360deg]" : ""}`}
                        />
                        <h1 className={`text-black origin-left font-medium text-2xl transition-transform duration-300 ${!open ? "scale-0" : ""}`}>
                            Bal<span className="text-black">i</span>yoni
                        </h1>
                    </div>
                    <ul className="pt-2">
    {Menus.map((menu, index) => (
        <React.Fragment key={index}>
            <li
                onClick={() => menu.submenu && handleSubmenuClick(index)}
                className={`text-black text-sm flex items-center gap-x-4 cursor-pointer p-2 ${menu.spacing && open ? "mt-6" : "mt-2"} hover:bg-red-600 hover:text-white rounded-md`}
            >
                <span className="text-2xl block float-left">
                    {menu.icon}
                </span>
                <span className={`font-medium text-base flex-1 ${!open ? "hidden" : ""}`}>
                    {menu.title}
                </span>
                {menu.submenu && open && (
                    <FaChevronDown
                        className={`cursor-pointer transition-transform duration-300 ${openSubmenuIndex === index ? "rotate-180" : ""}`}
                    />
                )}
            </li>
            {menu.submenu && openSubmenuIndex === index && (
                <ul className="overflow-hidden transition-all duration-500 ease-in-out max-h-96">
                    {menu.submenuItems.map((submenuItem, subIndex) => (
                        <li
                            key={subIndex}
                            className="text-black hover:text-white text-sm flex items-center gap-x-4 cursor-pointer p-2 hover:bg-red-600 rounded-md px-7"
                        >
                            {submenuItem.icon}
                            <span className="ml-4">{submenuItem.title}</span>
                        </li>
                    ))}
                </ul>
            )}
        </React.Fragment>
    ))}
</ul>
                </div>
                <div className="flex-1">
                    {/* Navbar */}
                    <div className="bg-white p-4 shadow-md flex items-center justify-between">
                        <div onClick={() => setOpen(!open)} className="cursor-pointer text-2xl text-gray-600">
                            <AiOutlineMenu />
                        </div>
                        <div className="flex items-center bg-gray-100 px-3 py-2 rounded-md w-1/3">
                            <CiSearch className="text-gray-500" />
                            <input type="search" placeholder="Search" className="bg-transparent focus:outline-none text-gray-700 ml-2 w-full" />
                        </div>
                        <h1 className="text-lg font-semibold text-gray-700">Home Page</h1>
                        <BsPersonCircle className="text-3xl text-gray-600 cursor-pointer" />
                    </div>
                    <div className="p-7">
                        <h1 className="text-2xl font-semibold">Home Page Content</h1>
                    </div>
                </div>
            </div>
        );
    }

    const root = document.getElementById("sidebar");
    if (root) {
        ReactDOM.createRoot(root).render(<App />);
    }
