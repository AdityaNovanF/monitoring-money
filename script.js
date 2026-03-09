document.addEventListener('DOMContentLoaded', () => {
    // --- Developer's Master Apps Script URL --- //
    const MASTER_SCRIPT_URL = 'https://script.google.com/macros/s/AKfycby3RA0dnaxppGCVdIlwijUUjAmGlI-s0neGdaGV2oLyYfcz4xoqU3zkw4LxzcRbQlhEnw/exec';

    // UI Elements
    const form = document.getElementById('transactionForm');
    const totalIncomeEl = document.getElementById('totalIncome');
    const totalExpenseEl = document.getElementById('totalExpense');
    const balanceEl = document.getElementById('balance');
    const submitBtn = document.getElementById('submitBtn');
    const cloudSyncStatus = document.getElementById('cloudSyncStatus');
    const userAccountName = document.getElementById('userAccountName');
    const sheetDocName = document.getElementById('sheetDocName');

    // Sidebar UI Elements
    const sidebar = document.getElementById('sidebar');
    const sidebarOverlay = document.getElementById('sidebarOverlay');
    const openSidebarBtn = document.getElementById('openSidebarBtn');
    const closeSidebarBtn = document.getElementById('closeSidebarBtn');
    const menuDashboard = document.getElementById('menuDashboard');
    const menuHistory = document.getElementById('menuHistory');
    const menuProfiles = document.getElementById('menuProfiles');
    const menuSettings = document.getElementById('menuSettings');
    const headerTitle = document.getElementById('headerTitle');

    // Views
    const viewDashboard = document.getElementById('viewDashboard');
    const viewHistory = document.getElementById('viewHistory');

    // Profile UI Elements
    const activeProfileIndicator = document.getElementById('activeProfileIndicator');
    const profilesListPage = document.getElementById('profilesListPage');
    const addProfileFormPage = document.getElementById('addProfileFormPage');
    const newProfileNamePage = document.getElementById('newProfileNamePage');

    // New UI Elements
    const amountDisplay = document.getElementById('amountDisplay');
    const amountVal = document.getElementById('amountVal');
    const searchInput = document.getElementById('searchInput');
    const filterBtns = document.querySelectorAll('.filter-btn');
    const sortOrder = document.getElementById('sortOrder');

    // List & Filter Elements
    const balanceLoading = document.getElementById('balanceLoading');
    const dashboardListLoading = document.getElementById('dashboardListLoading');
    const historyListLoading = document.getElementById('historyListLoading');
    const recentTransactionList = document.getElementById('recentTransactionList');
    const historyTransactionList = document.getElementById('historyTransactionList');
    const viewAllBtn = document.getElementById('viewAllBtn');

    const dateFilter = document.getElementById('dateFilter');
    const clearDateBtn = document.getElementById('clearDateBtn');
    const cloudSyncStatusDashboard = document.getElementById('cloudSyncStatusDashboard');
    const cloudSyncStatusHistory = document.getElementById('cloudSyncStatusHistory');

    // Settings
    let userSheetUrl = localStorage.getItem('userSheetUrl') || '';
    const sheetUrlInputPage = document.getElementById('sheetUrlInputPage');
    const saveSettingsBtnPage = document.getElementById('saveSettingsBtnPage');
    const settingsStatusMsgPage = document.getElementById('settingsStatusMsgPage');

    // State Variables
    let transactions = JSON.parse(localStorage.getItem('transactions')) || [];
    let currentFilter = 'All'; // All, Pemasukan, Pengeluaran
    let currentSort = 'Terbaru'; // Terbaru, Terlama, Tertinggi, Terendah
    let currentSearch = '';
    let currentDateFilter = ''; // YYYY-MM-DD
    let financeChartInstance = null;

    // Profiles State
    let profiles = JSON.parse(localStorage.getItem('profiles')) || [{ id: '1', name: 'Utama' }];
    let activeProfileId = localStorage.getItem('activeProfileId') || '1';

    // Helper: format currency IDR Text
    const formatCurrencyDisplay = (amountStr) => {
        let numberStr = amountStr.replace(/[^0-9]/g, '');
        if (!numberStr) return '';
        return new Intl.NumberFormat('id-ID').format(parseInt(numberStr));
    };

    // Helper: format currency IDR for HTML
    const formatCurrency = (amount) => {
        return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(amount);
    };

    // Helper: format date for display
    const formatDate = (dateString) => {
        const options = { year: 'numeric', month: 'short', day: 'numeric' };
        return new Date(dateString).toLocaleDateString('id-ID', options);
    };

    // -----------------------------------------
    // Event Listeners for Dynamic UI Inputs & Menus
    // -----------------------------------------

    // Sidebar Toggles
    const toggleSidebar = () => {
        const isClosed = sidebar.classList.contains('-translate-x-full');
        if (isClosed) {
            sidebar.classList.remove('-translate-x-full');
            sidebarOverlay.classList.remove('opacity-0', 'pointer-events-none');
        } else {
            sidebar.classList.add('-translate-x-full');
            sidebarOverlay.classList.add('opacity-0', 'pointer-events-none');
        }
    };

    if (openSidebarBtn) openSidebarBtn.addEventListener('click', toggleSidebar);
    if (closeSidebarBtn) closeSidebarBtn.addEventListener('click', toggleSidebar);
    if (sidebarOverlay) sidebarOverlay.addEventListener('click', toggleSidebar);

    // Navigation / View Switching
    const switchView = (viewName) => {
        if (window.innerWidth < 1024) {
            // Check if sidebar is open before trying to close
            if (!sidebar.classList.contains('-translate-x-full')) toggleSidebar();
        }

        // Hide all views
        [viewDashboard, viewHistory, document.getElementById('viewProfiles'), document.getElementById('viewSettings')].forEach(v => {
            if (v) v.classList.add('hidden', 'opacity-0');
        });

        // Reset all menu items
        [menuDashboard, menuHistory, menuProfiles, menuSettings].forEach(m => {
            if (m) {
                m.classList.remove('sidebar-link-active');
                m.classList.add('text-slate-600');
            }
        });

        const activeMenu = {
            'dashboard': menuDashboard,
            'history': menuHistory,
            'profiles': menuProfiles,
            'settings': menuSettings
        }[viewName];

        const activeView = {
            'dashboard': viewDashboard,
            'history': viewHistory,
            'profiles': document.getElementById('viewProfiles'),
            'settings': document.getElementById('viewSettings')
        }[viewName];

        if (activeView) {
            activeView.classList.remove('hidden');
        }

        if (activeMenu) {
            activeMenu.classList.add('sidebar-link-active');
            activeMenu.classList.remove('text-slate-600');
        }

        const viewTitles = {
            'dashboard': { main: 'Dashboard', sub: 'Overview' },
            'history': { main: 'Riwayat & Grafik', sub: 'Data Arus Kas' },
            'profiles': { main: 'Manajemen Profil', sub: 'Personalitas & Anggota' },
            'settings': { main: 'Pengaturan Cloud', sub: 'Konfigurasi Spreadsheet' }
        };

        const titleData = viewTitles[viewName] || { main: 'Aplikasi', sub: 'Monitoring' };

        // Update Desktop Header
        if (headerTitle) headerTitle.textContent = titleData.main;
        const headerSub = document.getElementById('headerSubtitle');
        if (headerSub) headerSub.textContent = titleData.sub;

        // Update Mobile Area
        const mTitle = document.getElementById('mobileHeaderTitle');
        const mSub = document.getElementById('mobileHeaderSubtitle');
        if (mTitle) mTitle.textContent = titleData.main;
        if (mSub) mSub.textContent = titleData.sub;

        if (viewName === 'dashboard') renderDashboardList();
        if (viewName === 'history') renderHistoryList();
        if (viewName === 'profiles') renderProfilesPage();
    };

    if (menuDashboard) menuDashboard.addEventListener('click', (e) => { e.preventDefault(); switchView('dashboard'); });
    if (menuHistory) menuHistory.addEventListener('click', (e) => { e.preventDefault(); switchView('history'); });
    if (menuProfiles) menuProfiles.addEventListener('click', (e) => { e.preventDefault(); switchView('profiles'); });
    if (menuSettings) menuSettings.addEventListener('click', (e) => { e.preventDefault(); switchView('settings'); });
    if (viewAllBtn) viewAllBtn.addEventListener('click', () => switchView('history'));

    // Currency Masking on Amount Input
    amountDisplay.addEventListener('input', function (e) {
        let val = this.value;
        val = val.replace(/[^0-9]/g, ''); // strip non numeric
        amountVal.value = val; // save raw value
        this.value = formatCurrencyDisplay(val); // show formatted
    });

    // Date Filter Logic
    if (dateFilter) {
        dateFilter.addEventListener('change', (e) => {
            currentDateFilter = e.target.value;
            if (currentDateFilter) {
                clearDateBtn.classList.remove('hidden');
            } else {
                clearDateBtn.classList.add('hidden');
            }
            renderHistoryList();
        });
    }

    if (clearDateBtn) {
        clearDateBtn.addEventListener('click', () => {
            dateFilter.value = '';
            currentDateFilter = '';
            clearDateBtn.classList.add('hidden');
            renderHistoryList();
        });
    }

    // Filtering
    filterBtns.forEach(btn => {
        btn.addEventListener('click', (e) => {
            // Remove active style from all
            filterBtns.forEach(b => {
                b.classList.remove('active', 'bg-white', 'text-slate-800', 'shadow-sm');
                b.classList.add('text-slate-500');
            });
            // Add active style to clicked
            const target = e.currentTarget;
            target.classList.remove('text-slate-500');
            target.classList.add('active', 'bg-white', 'text-slate-800', 'shadow-sm');

            currentFilter = target.getAttribute('data-filter');
            renderHistoryList();
        });
    });

    // Sorting
    sortOrder.addEventListener('change', (e) => {
        currentSort = e.target.value;
        renderHistoryList();
    });

    // Searching
    searchInput.addEventListener('input', (e) => {
        currentSearch = e.target.value.toLowerCase();
        renderHistoryList();
    });


    // -----------------------------------------
    // Set Loading States
    // -----------------------------------------
    const setLoading = (isLoading) => {
        if (isLoading) {
            if (balanceLoading) balanceLoading.classList.remove('hidden');
            if (balanceEl) balanceEl.classList.add('hidden');
            if (totalIncomeEl) totalIncomeEl.textContent = '...';
            if (totalExpenseEl) totalExpenseEl.textContent = '...';
            if (dashboardListLoading) dashboardListLoading.classList.remove('hidden');
            if (historyListLoading) historyListLoading.classList.remove('hidden');
            recentTransactionList.innerHTML = '';
            historyTransactionList.innerHTML = '';
        } else {
            if (balanceLoading) balanceLoading.classList.add('hidden');
            if (balanceEl) balanceEl.classList.remove('hidden');
            if (dashboardListLoading) dashboardListLoading.classList.add('hidden');
            if (historyListLoading) historyListLoading.classList.add('hidden');
        }
    };

    const updateCloudSyncStatus = (statusText, isError = false) => {
        const errorHtml = isError ? `<span class="bg-rose-50 text-rose-600 px-3 py-1.5 rounded-full text-xs font-medium border border-rose-100">${statusText}</span>`
            : `<span class="bg-emerald-50 text-emerald-600 px-3 py-1.5 rounded-full text-xs font-medium border border-emerald-100">${statusText}</span>`;
        if (cloudSyncStatusDashboard) cloudSyncStatusDashboard.innerHTML = errorHtml;
        if (cloudSyncStatusHistory) cloudSyncStatusHistory.innerHTML = errorHtml;
    };

    // -----------------------------------------
    // Main UI Updater function (Dashboard + Global Stats)
    // -----------------------------------------

    // Update UI Summary & List rendering logic
    const updateUI = () => {
        let totalIncome = 0;
        let totalExpense = 0;

        // Calculate Totals based on ALL transactions
        transactions.forEach((trx) => {
            if (trx.type === 'Pemasukan') totalIncome += parseFloat(trx.amount);
            else totalExpense += parseFloat(trx.amount);
        });

        const netBalance = totalIncome - totalExpense;
        const balanceEl = document.getElementById('balance');
        const balanceCard = document.getElementById('balanceCard');
        const balanceBadge = document.getElementById('balanceBadge');

        balanceEl.textContent = formatCurrency(netBalance);

        if (netBalance < 0) {
            balanceCard.classList.remove('card-gradient-surplus', 'card-glow-surplus');
            balanceCard.classList.add('card-gradient-deficit', 'card-glow-deficit');
            balanceBadge.innerHTML = `<span class="w-1.5 h-1.5 bg-rose-400 rounded-full mr-2"></span> Defisit`;
            balanceBadge.classList.remove('bg-white/10', 'text-white/70');
            balanceBadge.classList.add('bg-rose-500/20', 'text-rose-100');
        } else {
            balanceCard.classList.remove('card-gradient-deficit', 'card-glow-deficit');
            balanceCard.classList.add('card-gradient-surplus', 'card-glow-surplus');
            balanceBadge.innerHTML = `<span class="w-1.5 h-1.5 bg-emerald-400 rounded-full mr-2 animate-ping"></span> Aman`;
            balanceBadge.classList.add('bg-white/10', 'text-white/70');
            balanceBadge.classList.remove('bg-rose-500/20', 'text-rose-100');
        }

        document.getElementById('totalIncome').textContent = formatCurrency(totalIncome);
        document.getElementById('totalExpense').textContent = formatCurrency(totalExpense);

        localStorage.setItem('transactions', JSON.stringify(transactions));

        renderDashboardList();
        renderHistoryList();
    };

    // 1. Dashboard List Render (Latest 5, No Filters)
    const renderDashboardList = () => {
        recentTransactionList.innerHTML = '';
        if (transactions.length === 0) {
            recentTransactionList.innerHTML = `<div class="text-center py-8 text-slate-500 text-sm">Belum ada transaksi dicatat.</div>`;
            return;
        }

        // Sort by newest, take top 5
        const dashTx = [...transactions].sort((a, b) => new Date(b.timestamp) - new Date(a.timestamp)).slice(0, 5);

        dashTx.forEach((trx) => {
            const isIncome = trx.type === 'Pemasukan';
            const amountClass = isIncome ? 'text-emerald-500' : 'text-rose-500';
            const iconBg = isIncome ? 'bg-emerald-50' : 'bg-rose-50';
            const iconColor = isIncome ? 'text-emerald-500' : 'text-rose-500';
            const icon = isIncome
                ? `<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>`
                : `<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"></path></svg>`;

            const profInitial = trx.profile ? trx.profile.charAt(0).toUpperCase() : '?';
            const profileBadge = `<span class="inline-flex items-center justify-center h-5 w-5 rounded-lg bg-slate-100 text-[10px] font-black text-slate-500 mr-2 border border-slate-200" title="Dicatat oleh ${trx.profile || 'Utama'}">${profInitial}</span>`;

            recentTransactionList.innerHTML += `
                <div class="flex items-center justify-between p-5 bg-white rounded-3xl border border-slate-100 shadow-sm transition-all hover:shadow-xl hover:shadow-slate-200/50 group animate-slide-up">
                    <div class="flex items-center space-x-4">
                        <div class="p-3 ${iconBg} ${iconColor} rounded-2xl transition-transform group-hover:scale-110 shadow-sm">${icon}</div>
                        <div>
                            <p class="font-black text-slate-800 text-sm tracking-tight">${trx.category}</p>
                            <p class="text-[10px] text-slate-400 font-bold flex items-center mt-1 uppercase tracking-widest">${profileBadge} ${formatDate(trx.date)}</p>
                        </div>
                    </div>
                    <div class="font-black text-base ${amountClass} tracking-tight">
                        ${isIncome ? '+' : '-'}${formatCurrency(trx.amount)}
                    </div>
                </div>`;
        });
    };

    // 2. History List Render (Full, Filtered, Searched)
    const renderHistoryList = () => {
        historyTransactionList.innerHTML = '';

        // Apply Filters
        let processedTransactions = transactions.filter(trx => {
            // Type Filter
            if (currentFilter !== 'All' && trx.type !== currentFilter) return false;

            // Search Filter
            if (currentSearch) {
                const searchStr = `${trx.category} ${trx.note}`.toLowerCase();
                if (!searchStr.includes(currentSearch)) return false;
            }

            // Date Filter
            if (currentDateFilter && trx.date !== currentDateFilter) return false;

            return true;
        });

        // Apply Sort
        processedTransactions.sort((a, b) => {
            if (currentSort === 'Terbaru') return new Date(b.timestamp) - new Date(a.timestamp);
            if (currentSort === 'Terlama') return new Date(a.timestamp) - new Date(b.timestamp);
            if (currentSort === 'Tertinggi') return b.amount - a.amount;
            if (currentSort === 'Terendah') return a.amount - b.amount;
            return 0;
        });

        updateChart(processedTransactions); // Update chart based on filtered items

        if (processedTransactions.length === 0) {
            historyTransactionList.innerHTML = `
                <div class="text-center py-12 px-4 animate-fade-in">
                    <div class="w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                    </div>
                    <h3 class="font-semibold text-slate-700 mb-1">Tidak ada transaksi Ditemukan</h3>
                    <p class="text-slate-500 text-sm">Coba sesuaikan filter/pencarian Anda.</p>
                </div>
            `;
            return;
        }

        processedTransactions.forEach((trx) => {
            const isIncome = trx.type === 'Pemasukan';
            const amountClass = isIncome ? 'text-emerald-500' : 'text-rose-500';
            const iconBg = isIncome ? 'bg-emerald-50' : 'bg-rose-50';
            const iconColor = isIncome ? 'text-emerald-500' : 'text-rose-500';
            const icon = isIncome
                ? `<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>`
                : `<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"></path></svg>`;

            const profInitial = trx.profile ? trx.profile.charAt(0).toUpperCase() : '?';
            const profileBadge = `<span class="inline-flex items-center justify-center h-5 w-5 rounded-lg bg-slate-100 text-[10px] font-black text-slate-500 mr-2 border border-slate-200" title="Dicatat oleh ${trx.profile || 'Utama'}">${profInitial}</span>`;

            const div = document.createElement('div');
            div.className = 'flex items-center justify-between p-6 bg-white rounded-[2rem] border border-slate-100 shadow-sm transition-all hover:shadow-xl hover:shadow-indigo-100/30 group animate-slide-up';
            div.innerHTML = `
                <div class="flex items-center space-x-5">
                    <div class="p-3 ${iconBg} ${iconColor} rounded-2xl transition-transform group-hover:scale-110 shadow-sm">
                        ${icon}
                    </div>
                    <div>
                        <p class="font-black text-slate-800 tracking-tight">${trx.category}</p>
                        <p class="text-[10px] text-slate-400 font-bold flex items-center mt-1 uppercase tracking-widest">${profileBadge} ${formatDate(trx.date)} • ${trx.note || '-'}</p>
                    </div>
                </div>
                <div class="font-black text-lg ${amountClass} tracking-tight">
                    ${isIncome ? '+' : '-'}${formatCurrency(trx.amount)}
                </div>
            `;
            historyTransactionList.appendChild(div);
        });
    };

    // -----------------------------------------
    // Chart.js Configuration
    // -----------------------------------------
    const updateChart = (dataSubset) => {
        const ctx = document.getElementById('financeChart');
        if (!ctx) return;

        // Aggregate by date
        const grouped = {};
        dataSubset.forEach(trx => {
            if (!grouped[trx.date]) grouped[trx.date] = { inc: 0, exp: 0 };
            if (trx.type === 'Pemasukan') grouped[trx.date].inc += trx.amount;
            else grouped[trx.date].exp += trx.amount;
        });

        // Sort dates chronologically
        const sortedDates = Object.keys(grouped).sort();
        const incomeData = sortedDates.map(d => grouped[d].inc);
        const expenseData = sortedDates.map(d => grouped[d].exp);
        const labels = sortedDates.map(d => {
            const dateObj = new Date(d);
            return `${dateObj.getDate()} ${dateObj.toLocaleString('id-ID', { month: 'short' })}`; // e.g. 5 Des
        });

        if (financeChartInstance) {
            financeChartInstance.destroy();
        }

        financeChartInstance = new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels.length ? labels : ['Belum ada data'],
                datasets: [
                    {
                        label: 'Pemasukan',
                        data: incomeData.length ? incomeData : [0],
                        borderColor: '#10b981', // emerald-500
                        backgroundColor: '#10b98133',
                        tension: 0.4,
                        fill: true,
                        borderWidth: 2
                    },
                    {
                        label: 'Pengeluaran',
                        data: expenseData.length ? expenseData : [0],
                        borderColor: '#f43f5e', // rose-500
                        backgroundColor: '#f43f5e33',
                        tension: 0.4,
                        fill: true,
                        borderWidth: 2
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                        labels: { usePointStyle: true, boxWidth: 8 }
                    },
                    tooltip: {
                        mode: 'index',
                        intersect: false,
                        callbacks: {
                            label: function (context) {
                                let label = context.dataset.label || '';
                                if (label) label += ': ';
                                if (context.parsed.y !== null) {
                                    label += formatCurrency(context.parsed.y);
                                }
                                return label;
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { borderDash: [4, 4], color: '#f1f5f9' },
                        ticks: {
                            callback: function (value) {
                                if (value >= 1000000) return 'Rp ' + (value / 1000000).toFixed(1) + 'jt';
                                if (value >= 1000) return 'Rp ' + (value / 1000).toFixed(0) + 'k';
                                return 'Rp ' + value;
                            }
                        }
                    },
                    x: {
                        grid: { display: false }
                    }
                },
                interaction: {
                    mode: 'nearest',
                    axis: 'x',
                    intersect: false
                }
            }
        });
    }

    // -----------------------------------------
    // Network Logic (Sync / Fetch)
    // -----------------------------------------

    const setCloudStatus = (status, type) => {
        const loadingHtml = `<svg class="animate-spin -ml-1 mr-2 h-3 w-3 text-amber-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Memeriksa Data Cloud...`;
        const loadingClass = 'flex items-center text-xs font-medium bg-amber-50 text-amber-600 px-3 py-1.5 rounded-full';

        const syncedHtml = `<svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg> Tersinkron Cloud`;
        const syncedClass = 'flex items-center text-xs font-medium bg-emerald-50 text-emerald-600 px-3 py-1.5 rounded-full';

        let msg = type === 'nosheet' ? 'Lokal Saja' : 'Offline / Error';
        const errorHtml = `<svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg> ${msg}`;
        const errorClass = 'flex items-center text-xs font-medium bg-rose-50 text-rose-600 px-3 py-1.5 rounded-full';

        const targets = [cloudSyncStatusDashboard, cloudSyncStatusHistory];

        targets.forEach(el => {
            if (!el) return;
            if (status === 'loading') {
                el.innerHTML = loadingHtml;
                el.className = loadingClass;
            } else if (status === 'synced') {
                el.innerHTML = syncedHtml;
                el.className = syncedClass;
            } else if (status === 'error' || status === 'offline') {
                el.innerHTML = errorHtml;
                el.className = errorClass;
            }
        });
    }

    // Fetch All Data from Sheets on Load (Using JSONP due to Google Apps Script CORS)
    const fetchFromSheets = () => {
        if (!userSheetUrl) {
            setCloudStatus('error', 'nosheet');
            updateUI(); // just render local
            return;
        }

        setLoading(true);

        // Setup JSONP callback
        const callbackName = 'jsonpCallback_' + Math.round(100000 * Math.random());
        window[callbackName] = function (data) {
            if (data.result === 'success' && data.data) {
                transactions = data.data; // Overwrite local with cloud data
                setCloudStatus('synced');

                let owner = data.ownerEmail || 'Pengguna Tidak Diketahui';
                let doc = data.docName || 'Spreadsheet Terhubung';

                let username = owner.includes('@') ? owner.split('@')[0] : owner;
                if (username) username = username.charAt(0).toUpperCase() + username.slice(1);

                if (userAccountName) userAccountName.textContent = username;
                if (sheetDocName) sheetDocName.textContent = doc;

                localStorage.setItem('sheetOwnerName', username);
                localStorage.setItem('sheetDocName', doc);
            } else {
                console.error("Cloud fetch resulted in error or empty.", data);
                setCloudStatus('error');
            }

            // Cleanup
            delete window[callbackName];
            document.body.removeChild(script);
            setLoading(false);
            updateUI();
        };

        // Create script tag for JSONP
        const script = document.createElement('script');
        script.src = `${MASTER_SCRIPT_URL}?sheetUrl=${encodeURIComponent(userSheetUrl)}&callback=${callbackName}`;

        script.onerror = function () {
            console.error('Error fetching from sheets (Network or CORS).');
            setCloudStatus('error');
            delete window[callbackName];
            setLoading(false);
            updateUI();
        };

        document.body.appendChild(script);
    };

    // Sync 1 Transaction to Sheets (POST)
    const syncToSheets = async (data) => {
        if (!userSheetUrl) return true; // Lokal saja

        const payload = {
            sheetUrl: userSheetUrl,
            transaction: data
        };

        try {
            setCloudStatus('loading');
            const response = await fetch(MASTER_SCRIPT_URL, {
                method: 'POST',
                mode: 'no-cors',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(payload)
            });
            // no-cors will not return actual readable JSON response, but it works for simple insertion
            setTimeout(() => setCloudStatus('synced'), 1000);
            return true;
        } catch (error) {
            console.error('Error syncing to sheets:', error);
            setCloudStatus('error');
            return false;
        }
    };

    // -----------------------------------------
    // Form and Settings Handling
    // -----------------------------------------

    // Handle Form Submit
    form.addEventListener('submit', async (e) => {
        e.preventDefault();

        // Grab values from Radio and inputs
        const type = document.querySelector('input[name="type"]:checked').value;
        const amount = amountVal.value; // Hidden raw numeric value
        const category = document.getElementById('category').value;
        const date = document.getElementById('date').value;
        const note = document.getElementById('note').value;

        if (!amount || !category || !date) {
            alert('Harap isi nominal, kategori, dan tanggal.');
            return;
        }

        const btnText = submitBtn.innerHTML;
        submitBtn.innerHTML = `<svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white inline-block" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Menyimpan...`;
        submitBtn.disabled = true;

        const activeProf = profiles.find(p => p.id === activeProfileId) || profiles[0];

        const newTransaction = {
            id: Date.now().toString(),
            date,
            type,
            category: category.trim(),
            amount: parseFloat(amount),
            note: note.trim(),
            timestamp: new Date().toISOString(),
            profile: activeProf.name // Append local profile
        };

        // Attempt google sheet sync
        await syncToSheets(newTransaction);

        // Always save locally to keep UI working offline
        transactions.push(newTransaction);

        // Update UI
        updateUI();

        // Reset form
        form.reset();
        amountDisplay.value = '';
        amountVal.value = '';
        document.getElementById('date').valueAsDate = new Date(); // Reset to today

        // Reset radio back to Expense (default)
        document.getElementById('typeExpense').checked = true;

        submitBtn.innerHTML = btnText;
        submitBtn.disabled = false;
    });

    // --- Settings Logic removed as it's now a view --- //
    if (activeProfileIndicator) activeProfileIndicator.addEventListener('click', () => switchView('profiles'));

    // --- Profiles Management Logic (Halaman) --- //
    const renderProfilesPage = () => {
        if (!profilesListPage) return;
        profilesListPage.innerHTML = '';
        profiles.forEach(prof => {
            const isActive = prof.id === activeProfileId;
            const div = document.createElement('div');
            div.className = `p-6 rounded-[2rem] border-2 transition-all profile-card ${isActive ? 'bg-indigo-50/50 border-indigo-200 shadow-xl shadow-indigo-100/50' : 'bg-white border-slate-100 hover:border-indigo-100 hover:shadow-xl hover:shadow-slate-200/50'}`;

            div.innerHTML = `
                <div class="flex flex-col items-center text-center space-y-6">
                    <div class="relative group">
                        <div class="w-20 h-20 rounded-3xl ${isActive ? 'card-gradient text-white shadow-indigo-300/50' : 'bg-slate-200 text-slate-800 shadow-slate-200/50'} font-black flex items-center justify-center text-3xl shadow-lg transform transition-all group-hover:scale-110 active:scale-95 cursor-pointer" onclick="setActiveProfile('${prof.id}')">
                            ${prof.name.charAt(0).toUpperCase()}
                        </div>
                        ${isActive ? '<div class="absolute -right-2 -bottom-2 bg-emerald-500 text-white p-1.5 rounded-xl border-4 border-white shadow-lg"><svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg></div>' : ''}
                    </div>
                    <div>
                        <h4 class="font-black text-slate-800 text-lg">${prof.name}</h4>
                        <p class="text-[10px] text-slate-400 font-black uppercase tracking-[0.2em] mt-2">${isActive ? 'Profil Aktif' : 'Profil Anggota'}</p>
                    </div>
                    <div class="flex items-center space-x-2 w-full pt-2">
                        <button onclick="editProfile('${prof.id}')" class="flex-1 bg-white border border-slate-200 text-slate-600 hover:text-indigo-600 hover:bg-indigo-50 hover:border-indigo-200 py-3 rounded-2xl transition-all text-xs font-black flex items-center justify-center">
                            <svg class="w-3.5 h-3.5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v12a2 2 0 00-2 2h10a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                            Edit
                        </button>
                        ${profiles.length > 1 ? `
                        <button onclick="deleteProfile('${prof.id}')" class="bg-white border border-slate-200 text-slate-400 hover:text-rose-600 hover:bg-rose-50 hover:border-rose-200 py-3 rounded-2xl transition-all text-xs font-black flex items-center justify-center px-4">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                        </button>
                        ` : ''}
                    </div>
                </div>
            `;
            profilesListPage.appendChild(div);
        });
    };

    window.setActiveProfile = (id) => {
        activeProfileId = id;
        localStorage.setItem('activeProfileId', activeProfileId);
        updateActiveProfileIcon();
        renderProfilesPage();
    };

    window.editProfile = (id) => {
        const prof = profiles.find(p => p.id === id);
        if (!prof) return;
        const newName = prompt('Ubah nama profil menjadi:', prof.name);
        if (newName && newName.trim() !== '') {
            prof.name = newName.trim();
            localStorage.setItem('profiles', JSON.stringify(profiles));
            renderProfilesPage();
            updateActiveProfileIcon();
        }
    };

    window.deleteProfile = (id) => {
        if (profiles.length <= 1) return;
        if (confirm('Yakin ingin menghapus profil ini? Semua riwayat tetap tersimpan namun label profil akan berubah.')) {
            profiles = profiles.filter(p => p.id !== id);
            if (activeProfileId === id) activeProfileId = profiles[0].id;
            localStorage.setItem('profiles', JSON.stringify(profiles));
            localStorage.setItem('activeProfileId', activeProfileId);
            renderProfilesPage();
            updateActiveProfileIcon();
        }
    };

    const updateActiveProfileIcon = () => {
        const activeProf = profiles.find(p => p.id === activeProfileId) || profiles[0];
        if (activeProfileIndicator) {
            activeProfileIndicator.textContent = activeProf.name.charAt(0).toUpperCase();
            activeProfileIndicator.title = `Profil Aktif: ${activeProf.name}`;
        }
    };

    // --- Handled by addProfileFormPage listener below --- //

    if (addProfileFormPage) addProfileFormPage.addEventListener('submit', (e) => {
        e.preventDefault();
        const name = newProfileNamePage.value.trim();
        if (name) {
            profiles.push({ id: Date.now().toString(), name: name });
            localStorage.setItem('profiles', JSON.stringify(profiles));
            newProfileNamePage.value = '';
            renderProfilesPage();
        }
    });

    // --- Settings Modal Saving --- //
    // --- Settings Logic (Halaman) --- //
    if (saveSettingsBtnPage) {
        saveSettingsBtnPage.addEventListener('click', () => {
            const inputUrl = sheetUrlInputPage.value.trim();

            if (!inputUrl) {
                settingsStatusMsgPage.textContent = 'Wajib Diisi: Link Spreadsheet tidak boleh kosong.';
                settingsStatusMsgPage.className = 'text-sm text-center font-bold text-rose-600 mt-4 block p-4 bg-rose-50 rounded-2xl border border-rose-100';
                settingsStatusMsgPage.classList.remove('hidden');
                return;
            }

            if (!inputUrl.includes('docs.google.com/spreadsheets')) {
                settingsStatusMsgPage.textContent = 'Gagal: Link Spreadsheet tidak valid.';
                settingsStatusMsgPage.className = 'text-sm text-center font-bold text-rose-600 mt-4 block p-4 bg-rose-50 rounded-2xl border border-rose-100';
                settingsStatusMsgPage.classList.remove('hidden');
                return;
            }

            const urlChanged = userSheetUrl !== inputUrl;
            userSheetUrl = inputUrl;
            localStorage.setItem('userSheetUrl', userSheetUrl);

            settingsStatusMsgPage.textContent = 'Berhasil! Data sedang disinkronisasi...';
            settingsStatusMsgPage.className = 'text-sm text-center font-bold text-emerald-600 mt-4 block';
            settingsStatusMsgPage.classList.remove('hidden');

            setTimeout(() => {
                if (urlChanged) fetchFromSheets();
                // Optionally go back to dashboard after save
                setTimeout(() => switchView('dashboard'), 1500);
            }, 1000);
        });
    }

    // Initialize date to today
    document.getElementById('date').valueAsDate = new Date();

    // Restore Account Info from local storage initially
    const savedOwner = localStorage.getItem('sheetOwnerName');
    const savedDoc = localStorage.getItem('sheetDocName');
    if (savedOwner && userAccountName) userAccountName.textContent = savedOwner;
    if (savedDoc && sheetDocName) sheetDocName.textContent = savedDoc;

    // Load active profile icon on start
    updateActiveProfileIcon();

    // Initial Load
    fetchFromSheets();

    // Redirect to settings if no sheet URL found (onboarding)
    if (!userSheetUrl) {
        switchView('settings');
        // Show nudge message in settings
        if (settingsStatusMsgPage) {
            settingsStatusMsgPage.textContent = 'Halo! Harap hubungkan Spreadsheet Anda terlebih dahulu untuk memulai.';
            settingsStatusMsgPage.className = 'text-sm text-center font-bold text-indigo-600 mt-4 block p-4 bg-indigo-50 rounded-2xl border border-indigo-100 animate-pulse';
            settingsStatusMsgPage.classList.remove('hidden');
        }
    } else {
        switchView('dashboard');
    }

    // Pre-fill settings if exists
    if (sheetUrlInputPage) sheetUrlInputPage.value = userSheetUrl;
});
