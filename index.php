<!DOCTYPE html>
<html lang="id" class="antialiased">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Monitoring Money</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Chart.js CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    },
                    colors: {
                        primary: '#6366f1', // Indigo 500
                        secondary: '#8b5cf6', // Violet 500
                    }
                }
            }
        }
    </script>
    <style>
        /* Hide scrollbar completely but allow scrolling */
        ::-webkit-scrollbar { display: none; }
        * { -ms-overflow-style: none; scrollbar-width: none; }
        
        .glass { background: rgba(255, 255, 255, 0.85); backdrop-filter: blur(12px); border-bottom: 1px solid rgba(255, 255, 255, 0.3); }

        /* Smooth slide up animation */
        @keyframes slideUp { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
        .animate-slide-up { animation: slideUp 0.4s ease-out forwards; }

        /* Hide radio dots */
        input[type="radio"]:checked + label {
            background-color: #f1f5f9;
            border-color: #6366f1;
            color: #6366f1;
            font-weight: 600;
        }
    </style>
</head>
<body class="bg-slate-50 text-slate-800 h-screen flex overflow-hidden">

    <!-- Mobile Sidebar Overlay -->
    <div id="sidebarOverlay" class="fixed inset-0 bg-slate-900/50 z-40 lg:hidden opacity-0 pointer-events-none transition-opacity duration-300"></div>

    <!-- Sidebar Navigation -->
    <aside id="sidebar" class="fixed inset-y-0 left-0 z-50 w-72 bg-white border-r border-slate-100 shadow-2xl lg:shadow-none lg:static lg:translate-x-0 transform -translate-x-full transition-transform duration-300 flex flex-col">
        <!-- Sidebar Header (Logo) -->
        <div class="h-16 flex items-center px-6 border-b border-slate-100">
            <div class="w-8 h-8 rounded-full bg-gradient-to-tr from-primary to-secondary flex items-center justify-center shadow-lg shadow-indigo-200 mr-3">
                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            <h1 class="text-xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-slate-800 to-slate-600 tracking-tight">Monitoring Money</h1>
            <!-- Close Sidebar Btn (Mobile) -->
            <button id="closeSidebarBtn" class="lg:hidden ml-auto p-2 text-slate-400 hover:text-slate-600 focus:outline-none rounded-lg hover:bg-slate-100 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>

        <!-- Sidebar Menus -->
        <div class="flex-1 overflow-y-auto py-6 px-4 space-y-8">
            <!-- Nav Group 1 -->
            <div>
                <p class="px-4 text-xs font-semibold text-slate-400 uppercase tracking-wider mb-3">Keuangan</p>
                <nav class="space-y-1">
                    <a href="#" id="menuDashboard" class="sidebar-link active flex items-center px-4 py-3 bg-indigo-50 text-indigo-700 rounded-xl font-medium transition-colors">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
                        Dashboard
                    </a>
                    <a href="#" id="menuHistory" class="sidebar-link flex items-center px-4 py-3 text-slate-600 hover:bg-slate-50 hover:text-slate-900 rounded-xl font-medium transition-colors">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                        Riwayat Transaksi & Grafik
                    </a>
                </nav>
            </div>

            <!-- Nav Group 2 -->
            <div>
                <p class="px-4 text-xs font-semibold text-slate-400 uppercase tracking-wider mb-3">Kolaborasi</p>
                <nav class="space-y-1">
                    <a href="#" id="menuProfiles" class="flex items-center px-4 py-3 text-slate-600 hover:bg-slate-50 hover:text-slate-900 rounded-xl font-medium transition-colors">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                        Manajemen Profil
                    </a>
                </nav>
            </div>
        </div>
        
        <!-- Sidebar Footer -->
        <div class="h-16 px-6 border-t border-slate-100 flex items-center">
            <p class="text-xs text-slate-400 w-full text-center">Monitoring Money v2.0</p>
        </div>
    </aside>

    <!-- Main Content Wrapper -->
    <div class="flex-1 flex flex-col min-w-0 transition-all duration-300 relative">
        
        <!-- Header / Navbar -->
        <header class="glass sticky top-0 z-30 px-4 md:px-8 py-4 flex justify-between items-center shadow-sm">
            <div class="flex items-center">
                <!-- Hamburger Menu Btn (Mobile) -->
                <button id="openSidebarBtn" class="lg:hidden p-2 mr-3 text-slate-500 hover:text-slate-800 focus:outline-none rounded-lg hover:bg-slate-100 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"></path></svg>
                </button>
                <h2 id="headerTitle" class="text-lg font-bold text-slate-800 hidden sm:block tracking-tight">Dashboard Overview</h2>
            </div>
            <div class="flex items-center space-x-4">
                <!-- Account Info -->
                <div class="hidden sm:block text-right">
                    <p id="userAccountName" class="text-sm font-semibold text-slate-700">-</p>
                    <p id="sheetDocName" class="text-xs text-slate-400">Belum Terhubung</p>
                </div>
                <!-- Profile Avatar Indicator -->
                <div id="activeProfileIndicator" class="h-9 w-9 rounded-full bg-slate-200 border-2 border-white shadow-sm flex items-center justify-center cursor-pointer hover:ring-2 hover:ring-indigo-100 transition-all font-bold text-slate-600 text-sm" title="Profil Aktif">?</div>
                
                <!-- Settings Button -->
                <button id="openSettingsBtn" class="p-2 text-slate-400 hover:text-slate-600 transition-colors rounded-full hover:bg-slate-100 focus:outline-none" title="Pengaturan Spreadsheet">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                </button>
            </div>
        </header>

        <!-- Main Content Area -->
        <main class="flex-1 overflow-y-auto px-4 py-6 md:px-8 w-full pb-24 relative">
            
            <!-- ================= VIEW: DASHBOARD ================= -->
            <div id="viewDashboard" class="space-y-8 transition-opacity duration-300">
                <!-- Summary Dashboard Card -->
                <section class="bg-white rounded-3xl p-6 shadow-xl shadow-slate-200/50 border border-slate-100 relative overflow-hidden">
                    <!-- Decorative background blob -->
                    <div class="absolute -right-16 -top-16 w-48 h-48 bg-gradient-to-br from-primary/10 to-secondary/10 rounded-full blur-3xl"></div>
                    
                    <div class="relative z-10">
                        <h2 class="text-sm font-medium text-slate-500 mb-1 uppercase tracking-wider">Total Saldo Semua Riwayat</h2>
                        <!-- Loading State Saldo -->
                        <div id="balanceLoading" class="animate-pulse flex items-center mb-8 hidden">
                            <div class="h-10 bg-slate-200 rounded w-48"></div> 
                        </div>
                        <div class="text-4xl font-extrabold text-slate-800 mb-8 tracking-tight" id="balance">Rp 0</div>
                        
                        <div class="grid grid-cols-2 gap-4">
                            <!-- Income Card -->
                            <div class="bg-emerald-50 rounded-2xl p-4 border border-emerald-100/50 flex flex-col justify-center">
                                <div class="flex items-center space-x-2 mb-2">
                                    <div class="p-1.5 bg-emerald-100 rounded-lg">
                                        <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                                    </div>
                                    <span class="text-xs font-semibold text-emerald-700 uppercase">Pemasukan</span>
                                </div>
                                <div class="text-lg font-bold text-emerald-600" id="totalIncome">Rp 0</div>
                            </div>
                            
                            <!-- Expense Card -->
                            <div class="bg-rose-50 rounded-2xl p-4 border border-rose-100/50 flex flex-col justify-center">
                                <div class="flex items-center space-x-2 mb-2">
                                    <div class="p-1.5 bg-rose-100 rounded-lg">
                                        <svg class="w-4 h-4 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"></path></svg>
                                    </div>
                                    <span class="text-xs font-semibold text-rose-700 uppercase">Pengeluaran</span>
                                </div>
                                <div class="text-lg font-bold text-rose-600" id="totalExpense">Rp 0</div>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Main Layout Grid -->
                <div class="grid md:grid-cols-12 gap-8">
                    
                    <!-- Add Transaction Form -->
                    <section class="md:col-span-6 lg:col-span-5">
                        <div class="bg-white rounded-3xl p-6 shadow-xl shadow-slate-200/50 border border-slate-100 sticky top-24">
                            <h3 class="text-lg font-bold text-slate-800 mb-6 flex items-center">
                                <svg class="w-5 h-5 mr-2 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                                Catat Transaksi
                            </h3>
                            
                            <form id="transactionForm" class="space-y-5">
                                <!-- Jenis Transaksi (Radio Button Pilihan) -->
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 mb-2">Jenis</label>
                                    <div class="grid grid-cols-2 gap-3">
                                        <div>
                                            <input type="radio" name="type" id="typeExpense" value="Pengeluaran" class="peer hidden" checked>
                                            <label for="typeExpense" class="block text-center cursor-pointer border border-slate-200 text-slate-600 rounded-xl px-4 py-2.5 transition-all text-sm hover:bg-slate-50">
                                                Pengeluaran
                                            </label>
                                        </div>
                                        <div>
                                            <input type="radio" name="type" id="typeIncome" value="Pemasukan" class="peer hidden">
                                            <label for="typeIncome" class="block text-center cursor-pointer border border-slate-200 text-slate-600 rounded-xl px-4 py-2.5 transition-all text-sm hover:bg-slate-50">
                                                Pemasukan
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <!-- Nominal (Custom Format) -->
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 mb-1">Nominal</label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                            <span class="text-slate-500 font-medium sm:text-sm">Rp</span>
                                        </div>
                                        <input type="text" id="amountDisplay" placeholder="0" required class="w-full bg-slate-50 border border-slate-200 text-slate-800 rounded-xl pl-10 pr-4 py-3 focus:outline-none focus:ring-2 focus:ring-primary/50 focus:border-primary transition duration-200 text-lg font-semibold tracking-wide">
                                        <!-- Hidden field to store strictly numeric value -->
                                        <input type="hidden" id="amountVal" required>
                                    </div>
                                </div>

                                <!-- Kategori (Datalist / Ketik Sendiri) -->
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 mb-1">Kategori</label>
                                    <input type="text" id="category" list="categoryOptions" autocomplete="off" placeholder="Pilih atau ketik kategori..." required class="w-full bg-slate-50 border border-slate-200 text-slate-800 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-primary/50 focus:border-primary transition duration-200">
                                    <datalist id="categoryOptions">
                                        <option value="Makanan & Minuman">
                                        <option value="Transportasi">
                                        <option value="Belanja">
                                        <option value="Tagihan & Utilitas">
                                        <option value="Gaji">
                                        <option value="Hiburan">
                                    </datalist>
                                </div>

                                <!-- Tanggal -->
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 mb-1">Tanggal</label>
                                    <input type="date" id="date" required class="w-full bg-slate-50 border border-slate-200 text-slate-800 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-primary/50 focus:border-primary transition duration-200">
                                </div>

                                <!-- Catatan -->
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 mb-1">Catatan (opsional)</label>
                                    <textarea id="note" rows="2" placeholder="Detail transaksi..." class="w-full bg-slate-50 border border-slate-200 text-slate-800 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-primary/50 focus:border-primary transition duration-200 resize-none"></textarea>
                                </div>

                                <button type="submit" id="submitBtn" class="w-full bg-slate-800 hover:bg-slate-900 text-white font-medium py-3.5 px-4 rounded-xl shadow-lg shadow-slate-300 transition-all duration-200 transform hover:-translate-y-0.5 mt-2">
                                    Simpan Transaksi
                                </button>
                            </form>
                        </div>
                    </section>

                    <!-- Simplifed Recent History List (No Filters) -->
                    <section class="md:col-span-6 lg:col-span-7 flex flex-col">
                        <div class="bg-white rounded-3xl p-6 shadow-xl shadow-slate-200/50 border border-slate-100 flex-1 flex flex-col min-h-[500px]">
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6 pb-4 border-b border-slate-100">
                                <div>
                                    <h3 class="text-lg font-bold text-slate-800">Transaksi Terbaru</h3>
                                    <p class="text-xs text-slate-500 mt-1">Menampilkan 5 aktivitas terakhir</p>
                                </div>
                                <!-- Sync Cloud Status -->
                                <div id="cloudSyncStatusDashboard" class="flex items-center text-xs font-medium bg-amber-50 text-amber-600 px-3 py-1.5 rounded-full">
                                    <svg class="animate-spin -ml-1 mr-2 h-3 w-3 text-amber-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                    Memeriksa Cloud...
                                </div>
                            </div>
                            
                            <!-- Internal Load Skeleton -->
                            <div id="dashboardListLoading" class="hidden space-y-3">
                                <div class="animate-pulse flex items-center justify-between p-4 bg-slate-50 rounded-xl border border-slate-100">
                                    <div class="flex space-x-4"><div class="rounded-lg bg-slate-200 h-10 w-10"></div><div class="space-y-2"><div class="h-3 bg-slate-200 rounded w-24"></div><div class="h-2 bg-slate-200 rounded w-16"></div></div></div>
                                </div>
                                <div class="animate-pulse flex items-center justify-between p-4 bg-slate-50 rounded-xl border border-slate-100">
                                    <div class="flex space-x-4"><div class="rounded-lg bg-slate-200 h-10 w-10"></div><div class="space-y-2"><div class="h-3 bg-slate-200 rounded w-24"></div><div class="h-2 bg-slate-200 rounded w-16"></div></div></div>
                                </div>
                            </div>

                            <div id="recentTransactionList" class="space-y-3 flex-1 overflow-y-auto pr-1">
                                <!-- Populated by JS -->
                            </div>
                            
                            <button id="viewAllBtn" class="mt-6 w-full py-3 rounded-xl border-100 bg-slate-50 text-slate-600 text-sm font-semibold hover:bg-slate-100 transition-colors border shadow-sm">
                                Lihat Riwayat Selengkapnya &rarr;
                            </button>
                        </div>
                    </section>
                </div>
            </div>

            <!-- ================= VIEW: HISTORY & CHARTS ================= -->
            <div id="viewHistory" class="space-y-8 hidden opacity-0 transition-opacity duration-300">
                <!-- CHART SECTION -->
                <section class="bg-white rounded-3xl p-6 shadow-xl shadow-slate-200/50 border border-slate-100 relative">
                    <h3 class="text-lg font-bold text-slate-800 mb-2">Grafik Riwayat Keuangan</h3>
                    <p class="text-sm text-slate-500 mb-6">Melihat arus kas pemasukan dan pengeluaran berdasarkan waktu.</p>
                    
                    <div class="w-full h-64 md:h-80 relative">
                        <canvas id="financeChart"></canvas>
                    </div>
                </section>

                <!-- HISTORY LIST SECTION WITH FULL FILTERS -->
                <section class="bg-white rounded-3xl p-6 shadow-xl shadow-slate-200/50 border border-slate-100 flex flex-col min-h-[600px]">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6 pb-4 border-b border-slate-100">
                        <h3 class="text-lg font-bold text-slate-800">Semua Riwayat Transaksi</h3>
                        <!-- Status Cloud History -->
                        <div id="cloudSyncStatusHistory" class="flex items-center text-xs font-medium bg-amber-50 text-amber-600 px-3 py-1.5 rounded-full">
                            Memeriksa...
                        </div>
                    </div>

                    <!-- Advance Filters -->
                    <div class="space-y-4 mb-6 bg-slate-50 p-4 rounded-2xl border border-slate-100">
                        <div class="grid md:grid-cols-2 gap-4">
                            <!-- Search -->
                            <div class="relative">
                                <label class="block text-xs font-semibold text-slate-500 mb-1">Cari Kategori/Catatan</label>
                                <div class="absolute inset-y-0 left-0 pl-3 pt-6 flex items-center pointer-events-none">
                                    <svg class="h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                                </div>
                                <input type="search" id="searchInput" placeholder="Ketik kata kunci..." class="w-full bg-white border border-slate-200 text-slate-800 rounded-xl pl-9 pr-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/50 transition duration-200 shadow-sm">
                            </div>
                            
                            <!-- Date Filter -->
                            <div>
                                <label class="block text-xs font-semibold text-slate-500 mb-1">Filter Bulan/Hari</label>
                                <input type="date" id="dateFilter" class="w-full bg-white border border-slate-200 text-slate-800 rounded-xl px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/50 transition duration-200 shadow-sm">
                                <button id="clearDateBtn" class="text-xs text-indigo-600 mt-1 hover:underline hidden">Hapus Filter Tanggal</button>
                            </div>
                        </div>
                        
                        <!-- Toggle Filters -->
                        <div class="flex flex-wrap items-center justify-between gap-3 pt-2">
                            <div class="flex bg-slate-200/50 border border-slate-200 rounded-lg p-1 space-x-1" id="filterTypeContainer">
                                <button data-filter="All" class="filter-btn active bg-white text-slate-800 shadow-sm border border-slate-100 text-xs font-semibold px-3 py-1.5 rounded-md transition-all">Semua</button>
                                <button data-filter="Pemasukan" class="filter-btn text-slate-500 hover:text-slate-700 text-xs font-semibold px-3 py-1.5 rounded-md transition-all">Pemasukan</button>
                                <button data-filter="Pengeluaran" class="filter-btn text-slate-500 hover:text-slate-700 text-xs font-semibold px-3 py-1.5 rounded-md transition-all">Pengeluaran</button>
                            </div>
                            
                            <div class="flex items-center space-x-2">
                                <span class="text-xs text-slate-500 font-medium">Urutkan:</span>
                                <select id="sortOrder" class="bg-white border border-slate-200 shadow-sm text-slate-700 text-xs rounded-lg px-2 py-1.5 focus:outline-none focus:ring-1 focus:ring-primary cursor-pointer w-32">
                                    <option value="Terbaru">Terbaru</option>
                                    <option value="Terlama">Terlama</option>
                                    <option value="Tertinggi">Terbesar</option>
                                    <option value="Terendah">Terkecil</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- List Loading -->
                    <div id="historyListLoading" class="hidden space-y-3">
                        <div class="animate-pulse flex items-center justify-between p-4 bg-slate-50 rounded-xl">
                            <div class="flex space-x-4"><div class="rounded-lg bg-slate-200 h-10 w-10"></div><div class="h-3 bg-slate-200 rounded w-24"></div></div>
                        </div>
                        <div class="animate-pulse flex items-center justify-between p-4 bg-slate-50 rounded-xl">
                            <div class="flex space-x-4"><div class="rounded-lg bg-slate-200 h-10 w-10"></div><div class="h-3 bg-slate-200 rounded w-24"></div></div>
                        </div>
                    </div>

                    <!-- Huge List container -->
                    <div id="historyTransactionList" class="space-y-3 flex-1 overflow-y-auto pr-1">
                        <!-- Populated Javascript -->
                    </div>
                </section>
            </div>

        </main>    </div>

    <!-- Profile Management Modal -->
    <div id="profileModal" class="fixed inset-0 z-[100] hidden bg-slate-900/50 backdrop-blur-sm flex items-center justify-center p-4 opacity-0 transition-opacity duration-300">
        <div class="bg-white rounded-3xl p-6 md:p-8 shadow-2xl w-full max-w-md transform scale-95 transition-transform duration-300" id="profileModalContent">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-xl font-bold text-slate-800 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                    Pilih Profil Aktif
                </h3>
                <button id="closeProfileBtn" class="text-slate-400 hover:text-slate-600 focus:outline-none">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            
            <p class="text-sm text-slate-500 mb-4">Pilih profil untuk mencatat transaksi atas nama pengguna tersebut.</p>

            <!-- Profiles List Container -->
            <div id="profilesList" class="space-y-3 mb-6 max-h-[40vh] overflow-y-auto">
                <!-- Populated by JS -->
            </div>

            <hr class="border-slate-100 mb-4">

            <!-- Add Profile Form -->
            <form id="addProfileForm" class="flex gap-2">
                <input type="text" id="newProfileName" placeholder="Tambah Anggota (misal: Istri)" required class="flex-1 bg-slate-50 border border-slate-200 text-slate-800 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary/50 focus:border-primary transition duration-200">
                <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white p-2.5 rounded-xl transition-colors shadow-sm">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                </button>
            </form>
        </div>
    </div>

    <!-- Settings Modal -->
    <div id="settingsModal" class="fixed inset-0 z-[100] hidden bg-slate-900/50 backdrop-blur-sm flex items-center justify-center p-4 opacity-0 transition-opacity duration-300">
        <div class="bg-white rounded-3xl p-6 md:p-8 shadow-2xl w-full max-w-md transform scale-95 transition-transform duration-300" id="settingsModalContent">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-xl font-bold text-slate-800 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                    Pengaturan Spreadsheet
                </h3>
                <button id="closeSettingsBtn" class="text-slate-400 hover:text-slate-600 focus:outline-none">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            
            <div class="mb-6 space-y-3">
                <p class="text-sm text-slate-600">
                    Kini data Anda bisa otomatis tersimpan & tersinkronisasi. Ikuti 3 langkah mudah ini:
                </p>
                <ol class="text-sm text-slate-600 list-decimal list-inside space-y-1">
                    <li>Buat <a href="https://sheets.google.com/create" target="_blank" class="text-indigo-600 font-medium hover:underline">Google Sheet Baru</a> kosong.</li>
                    <li>Klik <b>Share (Bagikan)</b> di pojok kanan atas Sheet.</li>
                    <li>Ubah Hak Akses Umum menjadi <b>"Siapa saja yang memiliki link"</b> <i>(Anyone with the link)</i> dan pilih peran sebagai <b>Editor</b>.</li>
                </ol>
            </div>

            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Link Google Sheet Anda</label>
                    <input type="url" id="sheetUrlInput" placeholder="https://docs.google.com/spreadsheets/d/..." class="w-full bg-slate-50 border border-slate-200 text-slate-800 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-primary/50 focus:border-primary transition duration-200 text-sm">
                </div>
                <button id="saveSettingsBtn" class="w-full bg-slate-800 hover:bg-slate-900 text-white font-medium py-3 px-4 rounded-xl shadow-lg shadow-slate-300 transition-all duration-200 transform hover:-translate-y-0.5">
                    Simpan & Hubungkan
                </button>
                <p id="settingsStatusMsg" class="text-xs text-center font-medium hidden"></p>
            </div>
        </div>
    </div>

    <!-- App Script -->
    <script src="script.js"></script>
</body>
</html>
