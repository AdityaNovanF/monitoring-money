<!DOCTYPE html>
<html lang="id" class="antialiased">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Monitoring Money</title>
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="assets/logo-mm.png">
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
        /* Custom Scrollbar */
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }
        ::-webkit-scrollbar-thumb:hover { background: #cbd5e1; }
        * { -ms-overflow-style: auto; scrollbar-width: thin; scrollbar-color: #e2e8f0 transparent; }
        
        .glass { background: rgba(255, 255, 255, 0.7); backdrop-filter: blur(20px) saturate(180%); -webkit-backdrop-filter: blur(20px) saturate(180%); border-bottom: 1px solid rgba(255, 255, 255, 0.3); }
        .glass-sidebar { background: rgba(255, 255, 255, 0.8); backdrop-filter: blur(20px); -webkit-backdrop-filter: blur(20px); }
        
        .card-gradient { background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%); }
        .card-gradient-surplus { background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%); }
        .card-gradient-deficit { background: linear-gradient(135deg, #475569 0%, #e11d48 100%); }
        .card-glow-surplus { box-shadow: 0 10px 40px -10px rgba(99, 102, 241, 0.5); }
        .card-glow-deficit { box-shadow: 0 10px 40px -10px rgba(225, 29, 72, 0.5); }
        
        /* Entrance Animations */
        @keyframes fadeInScale { from { opacity: 0; transform: scale(0.98) translateY(10px); } to { opacity: 1; transform: scale(1) translateY(0); } }
        .animate-premium { animation: fadeInScale 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards; }
        
        /* Floating Animation */
        @keyframes float { 0% { transform: translateY(0px); } 50% { transform: translateY(-5px); } 100% { transform: translateY(0px); } }
        .animate-float { animation: float 3s ease-in-out infinite; }

        .sidebar-link { transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); position: relative; }
        .sidebar-link-active { background: linear-gradient(90deg, rgba(99, 102, 241, 0.1) 0%, rgba(139, 92, 246, 0.05) 100%); border-left: 4px solid #6366f1; color: #6366f1 !important; font-weight: 700; }
        
        input:focus { border-color: #6366f1; box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.1); }
        .profile-card:hover { transform: translateY(-4px); box-shadow: 0 20px 25px -5px rgb(0 0 0 / 0.1), 0 8px 10px -6px rgb(0 0 0 / 0.1); }
        
        /* Smooth Entrance */
        .animate-slide-up { animation: fadeInScale 0.4s ease-out forwards; }

        input[type="radio"]:checked + label {
            background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
            border-color: transparent;
            color: white;
            font-weight: 700;
            box-shadow: 0 4px 12px rgba(99, 102, 241, 0.3);
        }
    </style>
</head>
<body class="bg-slate-50 text-slate-800 h-screen flex overflow-hidden">

    <!-- Mobile Sidebar Overlay -->
    <div id="sidebarOverlay" class="fixed inset-0 bg-slate-900/50 z-40 lg:hidden opacity-0 pointer-events-none transition-opacity duration-300"></div>

    <!-- Sidebar Navigation -->
    <aside id="sidebar" class="fixed inset-y-0 left-0 z-50 w-72 glass-sidebar border-r border-slate-200/50 shadow-2xl lg:shadow-indigo-100/20 lg:static lg:translate-x-0 transform -translate-x-full transition-all duration-300 flex flex-col lg:m-4 lg:rounded-3xl lg:border lg:h-[calc(100vh-2rem)]">
        <!-- Sidebar Header (Logo) -->
        <div class="h-32 flex items-center justify-center relative">
            <div class="flex items-center justify-center h-full w-full">
                <img src="assets/logo-mm.png" alt="Logo" class="h-28 w-auto object-contain" onerror="this.onerror=null; this.parentElement.querySelector('.fallback-logo').classList.remove('hidden'); this.style.display='none';">
                <div class="fallback-logo hidden h-16 w-16 bg-gradient-to-tr from-primary to-secondary flex items-center justify-center shadow-lg shadow-indigo-200 rounded-full">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
            </div>
            <!-- Close Sidebar Btn (Mobile) -->
            <button id="closeSidebarBtn" class="lg:hidden absolute right-4 p-2 text-slate-400 hover:text-slate-600 focus:outline-none rounded-lg hover:bg-slate-100 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>

        <!-- Sidebar Menus -->
        <div class="flex-1 overflow-y-auto py-6 px-4 space-y-8">
            <!-- Nav Group 1 -->
            <div>
                <p class="px-4 text-xs font-semibold text-slate-400 uppercase tracking-wider mb-3">Keuangan</p>
                <nav class="space-y-1">
                    <a href="#" id="menuDashboard" class="sidebar-link flex items-center px-4 py-3 rounded-xl font-medium">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
                        Dashboard
                    </a>
                    <a href="#" id="menuHistory" class="sidebar-link flex items-center px-4 py-3 text-slate-600 rounded-xl font-medium">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                        Riwayat Transaksi & Grafik
                    </a>
                </nav>
            </div>

            <!-- Nav Group 2 -->
            <div>
                <p class="px-4 text-xs font-semibold text-slate-400 uppercase tracking-wider mb-3">Sistem & Kolaborasi</p>
                <nav class="space-y-1">
                    <a href="#" id="menuProfiles" class="sidebar-link flex items-center px-4 py-3 text-slate-600 rounded-xl font-medium">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                        Manajemen Profil
                    </a>
                    <a href="#" id="menuSettings" class="sidebar-link flex items-center px-4 py-3 text-slate-600 rounded-xl font-medium">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                        Pengaturan Cloud
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
        <header class="glass sticky top-2 z-30 mx-4 md:mx-8 my-4 px-6 py-4 flex justify-between items-center rounded-3xl shadow-xl shadow-slate-200/20 border border-white/50 relative overflow-hidden">
            <div class="absolute inset-0 bg-white/40 backdrop-blur-3xl -z-10"></div>
            <div class="flex items-center relative z-10">
                <!-- Hamburger Menu Btn (Mobile) -->
                <button id="openSidebarBtn" class="lg:hidden p-2.5 mr-4 text-slate-500 hover:text-indigo-600 focus:outline-none rounded-2xl hover:bg-white/50 transition-all duration-300 shadow-sm border border-transparent hover:border-indigo-100/50">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"></path></svg>
                </button>
                <div class="hidden lg:flex flex-col">
                    <h2 id="headerTitle" class="text-sm font-black text-slate-800 tracking-tight uppercase">Dashboard</h2>
                    <p id="headerSubtitle" class="text-[10px] text-slate-400 font-bold uppercase tracking-[0.2em] leading-none mt-1">Overview</p>
                </div>
            </div>
            
            <div class="absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 lg:hidden">
                <img src="assets/logo-mm.png" alt="Logo" class="h-16 w-auto object-contain">
            </div>

            <div class="flex items-center space-x-5 relative z-10">
                <!-- Account Info -->
                <div class="hidden md:block text-right">
                    <p id="userAccountName" class="text-xs font-black text-slate-800 uppercase tracking-tight">-</p>
                    <p id="sheetDocName" class="text-[9px] text-slate-400 font-bold uppercase tracking-widest mt-0.5">Offline Mode</p>
                </div>
                <!-- Profile Avatar Indicator -->
                <div id="activeProfileIndicator" class="h-10 w-10 rounded-2xl bg-white border border-slate-100 shadow-sm flex items-center justify-center cursor-pointer hover:shadow-lg hover:shadow-indigo-100/50 hover:border-indigo-100 hover:-translate-y-0.5 transition-all duration-300 font-black text-indigo-600 text-sm">?</div>
            </div>
        </header>

        <!-- Main Content Area -->
        <main class="flex-1 overflow-y-auto px-4 py-6 md:px-8 w-full pb-24 relative scroll-smooth">
            
            <!-- Mobile Page Title Area -->
            <div class="lg:hidden mb-6 animate-premium">
                <h2 id="mobileHeaderTitle" class="text-xl font-black text-slate-800 tracking-tight uppercase">Dashboard</h2>
                <p id="mobileHeaderSubtitle" class="text-[9px] text-slate-400 font-black uppercase tracking-[0.3em] leading-none mt-1.5 pl-0.5">Overview</p>
            </div>
            <!-- ================= VIEW: DASHBOARD ================= -->
            <div id="viewDashboard" class="space-y-8 animate-premium">
                <!-- Summary Dashboard Card -->
                <section id="balanceCard" class="card-gradient-surplus rounded-[2.5rem] p-8 shadow-2xl card-glow-surplus relative overflow-hidden text-white transition-all duration-500">
                    <!-- Decorative elements -->
                    <div class="absolute -right-10 -top-10 w-64 h-64 bg-white/10 rounded-full blur-3xl animate-pulse"></div>
                    <div class="absolute -left-10 -bottom-10 w-48 h-48 bg-white/5 rounded-full blur-2xl"></div>
                    
                    <div class="relative z-10">
                        <div class="flex items-center justify-between mb-2">
                            <h2 class="text-[10px] font-black text-white/70 uppercase tracking-[0.2em]">Total Saldo Tersedia</h2>
                            <div id="balanceBadge" class="flex items-center bg-white/10 backdrop-blur-md px-3 py-1 rounded-full text-[9px] font-black border border-white/20 uppercase tracking-widest transition-all">
                                <span class="w-1.5 h-1.5 bg-emerald-400 rounded-full mr-2 animate-ping"></span> Aman
                            </div>
                        </div>

                        <!-- Shimmer Loading for Balance -->
                        <div id="balanceLoading" class="animate-pulse flex items-center mb-8 hidden">
                            <div class="h-12 bg-white/20 rounded-2xl w-64 shadow-inner"></div> 
                        </div>
                        
                        <div class="text-5xl font-black mb-10 tracking-tighter drop-shadow-sm" id="balance">Rp 0</div>
                        
                        <div class="grid grid-cols-2 gap-6">
                            <!-- Income Card -->
                            <div class="bg-white/10 backdrop-blur-xl rounded-[2rem] p-5 border border-white/20 flex flex-col justify-center transition-all hover:bg-white/20">
                                <div class="flex items-center space-x-3 mb-3">
                                    <div class="p-2 bg-white/10 rounded-xl">
                                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                                    </div>
                                    <span class="text-[9px] font-black uppercase tracking-widest text-white/70">Pemasukan</span>
                                </div>
                                <div class="text-xl font-black" id="totalIncome">Rp 0</div>
                            </div>
                            
                            <!-- Expense Card -->
                            <div class="bg-white/10 backdrop-blur-xl rounded-[2rem] p-5 border border-white/20 flex flex-col justify-center transition-all hover:bg-white/20">
                                <div class="flex items-center space-x-3 mb-3">
                                    <div class="p-2 bg-white/10 rounded-xl">
                                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"></path></svg>
                                    </div>
                                    <span class="text-[9px] font-black uppercase tracking-widest text-white/70">Pengeluaran</span>
                                </div>
                                <div class="text-xl font-black" id="totalExpense">Rp 0</div>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Main Layout Grid -->
                <div class="grid md:grid-cols-12 gap-8">
                    
                    <!-- Add Transaction Form -->
                    <section class="md:col-span-6 lg:col-span-5">
                        <div class="bg-white rounded-[2rem] p-8 shadow-xl shadow-slate-200/40 border border-slate-100 sticky top-24 transition-all hover:shadow-2xl">
                            <h3 class="text-xl font-black text-slate-800 mb-8 flex items-center">
                                <div class="p-2 bg-indigo-50 rounded-xl mr-3">
                                    <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                                </div>
                                Transaksi Baru
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
                        <div class="bg-white rounded-[2rem] p-8 shadow-xl shadow-slate-200/40 border border-slate-100 flex-1 flex flex-col min-h-[600px]">
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8 pb-6 border-b border-slate-50">
                                <div>
                                    <h3 class="text-xl font-black text-slate-800 uppercase tracking-tight">Aktivitas Terakhir</h3>
                                    <p class="text-xs text-slate-400 font-medium mt-1">Status Keuangan Real-Time</p>
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
            <div id="viewHistory" class="space-y-8 hidden animate-premium">
                <!-- CHART SECTION -->
                <section class="bg-white rounded-[2.5rem] p-8 shadow-xl shadow-slate-200/40 border border-slate-100 relative overflow-hidden">
                    <div class="absolute -right-16 -top-16 w-64 h-64 bg-primary/5 rounded-full blur-3xl"></div>
                    <div class="relative z-10">
                        <h3 class="text-xl font-black text-slate-800 mb-2 tracking-tight uppercase">Analisis Arus Kas</h3>
                        <p class="text-xs text-slate-400 font-medium mb-8">Visualisasi pemasukan dan pengeluaran Anda.</p>
                        
                        <div class="w-full h-64 md:h-80 relative">
                            <canvas id="financeChart"></canvas>
                        </div>
                    </div>
                </section>

                <!-- HISTORY LIST SECTION WITH FULL FILTERS -->
                <section class="bg-white rounded-[2.5rem] p-8 shadow-xl shadow-slate-200/40 border border-slate-100 flex flex-col min-h-[600px] relative overflow-hidden mt-8">
                    <div class="absolute -left-16 -top-16 w-64 h-64 bg-secondary/5 rounded-full blur-3xl"></div>
                    <div class="relative z-10 flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8 pb-6 border-b border-slate-50">
                        <h3 class="text-xl font-black text-slate-800 uppercase tracking-tight">Semua Riwayat</h3>
                        <!-- Status Cloud History -->
                        <div id="cloudSyncStatusHistory" class="flex items-center text-[10px] font-black bg-amber-50 text-amber-600 px-4 py-2 rounded-full border border-amber-100 uppercase tracking-widest">
                            Syncing...
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

            <!-- ================= VIEW: PROFILES ================= -->
            <div id="viewProfiles" class="space-y-8 hidden animate-premium">
                <section class="bg-white rounded-[2.5rem] p-8 md:p-10 shadow-xl shadow-slate-200/40 border border-slate-100 flex flex-col min-h-[600px] relative overflow-hidden">
                    <div class="absolute -right-20 -bottom-20 w-80 h-80 bg-secondary/5 rounded-full blur-3xl"></div>
                    <div class="relative z-10">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8 pb-6 border-b border-slate-100">
                            <div>
                                <h3 class="text-xl font-black text-slate-800 uppercase tracking-tight">Manajemen Anggota</h3>
                                <p class="text-xs text-slate-400 font-medium mt-1">Personalisasi pencatatan keuangan keluarga Anda.</p>
                            </div>
                        </div>

                    <!-- Add Profile Form -->
                    <div class="mb-8 p-4 bg-slate-50 rounded-2xl border border-slate-100">
                        <label class="block text-sm font-semibold text-slate-700 mb-2">Tambah Profil Baru</label>
                        <form id="addProfileFormPage" class="flex gap-2">
                            <input type="text" id="newProfileNamePage" placeholder="Nama Anggota (misal: Istri)" required class="flex-1 bg-white border border-slate-200 text-slate-800 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary/50 transition duration-200 shadow-sm">
                            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2.5 rounded-xl transition-all shadow-md font-medium text-sm flex items-center">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                                Tambah
                            </button>
                        </form>
                    </div>

                    <!-- Profiles List Container -->
                    <div id="profilesListPage" class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                        <!-- Populated by JS -->
                    </div>
                </section>
            </div>

                <!-- ================= VIEW: SETTINGS ================= -->
                <section id="viewSettings" class="hidden space-y-8 animate-premium pb-32">
                    <div class="px-2">
                        <h3 class="text-xs font-black text-slate-400 uppercase tracking-widest mb-6 px-1">Integrasi Google Sheets</h3>
                    </div>
                    <div class="bg-white rounded-[2.5rem] p-10 shadow-2xl shadow-slate-200/40 border border-slate-100 max-w-2xl mx-auto relative overflow-hidden">
                    <div class="absolute -right-20 -top-20 w-80 h-80 bg-indigo-500/5 rounded-full blur-3xl"></div>
                    <div class="relative z-10">
                        <h3 class="text-2xl font-black text-slate-800 mb-8 flex items-center uppercase tracking-tight">
                            <div class="p-3 bg-indigo-50 rounded-2xl mr-4">
                                <svg class="w-6 h-6 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                            </div>
                            Spreadsheet Cloud
                        </h3>
                        
                        <div class="mb-10 p-6 bg-slate-50 rounded-[2rem] border border-slate-100">
                            <p class="text-xs font-black text-slate-800 mb-4 uppercase tracking-widest">Langkah Sinkronisasi:</p>
                            <ol class="text-xs text-slate-500 list-decimal list-inside space-y-3 leading-relaxed font-medium">
                                <li>Buat <a href="https://sheets.google.com/create" target="_blank" class="text-indigo-600 font-bold hover:underline">Google Sheet Baru</a>.</li>
                                <li>Klik <b>Share (Bagikan)</b> di pojok kanan atas Sheet.</li>
                                <li>Ubah Hak Akses Umum menjadi <b>"Anyone with the link"</b>.</li>
                                <li>Pilih peran sebagai <b>Editor</b>.</li>
                                <li>Salin link Sheet tersebut dan tempel di bawah ini.</li>
                            </ol>
                        </div>
    
                        <div class="space-y-8">
                            <div>
                                <label class="block text-xs font-black text-slate-500 mb-3 uppercase tracking-widest">Link Google Spreadsheet</label>
                                <input type="url" id="sheetUrlInputPage" placeholder="https://docs.google.com/spreadsheets/d/..." class="w-full bg-slate-50 border border-slate-200 text-slate-800 rounded-2xl px-6 py-4 focus:outline-none transition duration-200 text-sm shadow-sm font-medium">
                            </div>
                            <button id="saveSettingsBtnPage" class="w-full card-gradient hover:shadow-2xl hover:shadow-indigo-400/40 text-white font-black py-5 px-6 rounded-3xl shadow-xl shadow-indigo-200/50 transition-all duration-300 transform hover:-translate-y-1 active:scale-95 uppercase tracking-widest text-xs relative overflow-hidden group">
                                <span class="relative z-10">Hubungkan Spreadsheet</span>
                                <div class="absolute inset-0 bg-white/20 translate-y-full group-hover:translate-y-0 transition-transform duration-300"></div>
                            </button>
                            <div id="settingsStatusMsgPage" class="text-sm text-center font-bold hidden"></div>
                        </div>
                    </div>
                </section>
            </div>

        </main>    </div>

    <!-- App Script -->
    <script src="script.js"></script>
</body>
</html>
