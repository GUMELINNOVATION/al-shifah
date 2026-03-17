<?php
// transparency.php
require_once 'includes/data.php';

$pageTitle = "Transparency & Accountability";

// Get unique years from financial data
$years = array_unique(array_map(function($d) { return $d['year']; }, $FINANCIAL_DATA));
rsort($years);

$selectedYear = isset($_GET['year']) ? $_GET['year'] : $years[0];

// Filter data for selected year
$currentYearData = array_filter($FINANCIAL_DATA, function($d) use ($selectedYear) {
    return $d['year'] === $selectedYear;
});

$totalAmount = array_reduce($currentYearData, function($sum, $item) {
    return $sum + $item['amount'];
}, 0);

$programData = array_filter($currentYearData, function($d) { return $d['category'] === 'Programs'; });
$programEfficiency = $totalAmount > 0 ? (reset($programData)['amount'] / $totalAmount * 100) : 0;

include_once 'includes/header.php';
?>

<!-- Add Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 space-y-24">
    <!-- Page Header -->
    <section class="text-center max-w-3xl mx-auto animate-in fade-in duration-700">
        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-emerald-50 text-emerald-600 text-[10px] font-bold uppercase tracking-widest mb-6 border border-emerald-100">
            <i data-lucide="shield-check" class="w-3.5 h-3.5"></i> Fiscal Responsibility
        </div>
        <h1 class="text-4xl md:text-5xl font-bold text-slate-900 mb-6">Transparency & Accountability</h1>
        <p class="text-lg text-slate-500 leading-relaxed">
            At Al-Shifah Charity Foundation, our constitution mandates absolute clarity in our operations. Explore our audited reports to see how your support drives community impact.
        </p>
    </section>

    <!-- Annual Summary Controls -->
    <section class="bg-white rounded-[3rem] shadow-xl shadow-slate-200/40 border border-slate-100 overflow-hidden">
        <div class="p-8 md:p-12 border-b border-slate-50 flex flex-col md:flex-row md:items-center justify-between gap-8 bg-slate-50/30">
            <div>
                <h2 class="text-2xl font-bold text-slate-900 flex items-center gap-3">
                    <i data-lucide="calendar" class="text-emerald-500 w-6 h-6"></i> Annual Financial Summary
                </h2>
                <p class="text-slate-500 mt-1">Review our performance and allocation for the selected fiscal year.</p>
            </div>
            <div class="flex items-center gap-4 bg-white p-2 rounded-2xl shadow-sm border border-slate-100">
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest px-4 border-r border-slate-100">Filter Year</span>
                <div class="flex gap-1">
                    <?php foreach ($years as $y): ?>
                        <a href="?year=<?php echo $y; ?>" 
                           class="px-6 py-2 rounded-xl text-sm font-bold transition-all <?php echo $selectedYear === $y ? 'bg-emerald-600 text-white shadow-lg shadow-emerald-100' : 'text-slate-500 hover:bg-slate-50'; ?>">
                            <?php echo $y; ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <div class="p-8 md:p-12">
            <div class="grid lg:grid-cols-3 gap-12">
                <!-- Pie Chart Component -->
                <div class="lg:col-span-1 space-y-8">
                    <div class="h-[280px] relative">
                        <canvas id="pieChart"></canvas>
                    </div>
                    <div class="space-y-4">
                        <?php 
                        $colors = ['#10b981', '#059669', '#34d399', '#6ee7b7', '#a7f3d0'];
                        $i = 0;
                        foreach ($currentYearData as $item): 
                            $color = $colors[$i % count($colors)];
                        ?>
                            <div class="flex justify-between items-center p-4 bg-slate-50 rounded-2xl">
                                <div class="flex items-center gap-3">
                                    <div class="w-3 h-3 rounded-full" style="background-color: <?php echo $color; ?>"></div>
                                    <span class="text-sm font-bold text-slate-700"><?php echo $item['category']; ?></span>
                                </div>
                                <span class="text-sm font-bold text-slate-900">₦<?php echo number_format($item['amount']); ?></span>
                            </div>
                        <?php $i++; endforeach; ?>
                        <div class="pt-4 border-t border-slate-100 flex justify-between items-center px-4">
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Total Fiscal Budget</span>
                            <span class="text-xl font-bold text-emerald-600">₦<?php echo number_format($totalAmount); ?></span>
                        </div>
                    </div>
                </div>

                <!-- Bar Chart and Details -->
                <div class="lg:col-span-2 space-y-12">
                    <div class="h-[300px] w-full">
                        <canvas id="barChart"></canvas>
                    </div>

                    <div class="grid md:grid-cols-2 gap-6">
                        <div class="p-8 bg-emerald-50 rounded-[2.5rem] border border-emerald-100 relative overflow-hidden group">
                            <div class="absolute -right-4 -bottom-4 w-24 h-24 bg-emerald-100 rounded-full blur-2xl opacity-50 group-hover:scale-150 transition-transform duration-700"></div>
                            <div class="relative z-10">
                                <i data-lucide="trending-up" class="text-emerald-600 mb-4 w-6 h-6"></i>
                                <div class="text-3xl font-bold text-emerald-900 mb-1">
                                    <?php echo number_format($programEfficiency, 1); ?>%
                                </div>
                                <div class="text-[10px] font-bold text-emerald-600 uppercase tracking-widest">Program Efficiency</div>
                                <p class="text-xs text-emerald-800/60 mt-4 leading-relaxed font-medium">Of all funds were directly applied to community relief and essential field services.</p>
                            </div>
                        </div>

                        <div class="p-8 bg-slate-900 rounded-[2.5rem] text-white relative overflow-hidden group">
                            <div class="absolute -right-4 -bottom-4 w-24 h-24 bg-emerald-500 rounded-full blur-3xl opacity-20 group-hover:scale-150 transition-transform duration-700"></div>
                            <div class="relative z-10">
                                <i data-lucide="landmark" class="text-emerald-400 mb-4 w-6 h-6"></i>
                                <div class="text-3xl font-bold text-white mb-1">Audited</div>
                                <div class="text-[10px] font-bold text-emerald-400 uppercase tracking-widest">Compliance Status</div>
                                <p class="text-xs text-slate-400 mt-4 leading-relaxed">Independent verification confirms adherence to Al-Shifah Constitutional Article 13.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Detailed Financial Breakdown Table -->
    <section class="bg-white rounded-[3rem] shadow-xl shadow-slate-200/40 border border-slate-100 overflow-hidden">
        <div class="p-8 md:p-12 border-b border-slate-50 flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div>
                <h2 class="text-2xl font-bold text-slate-900 flex items-center gap-3">
                    <i data-lucide="file-text" class="text-emerald-500 w-6 h-6"></i> Granular Fiscal Report
                </h2>
                <p class="text-slate-500 mt-1">A transparent view of historical expenditures across all years.</p>
            </div>
            <button class="flex items-center gap-2 bg-slate-900 text-white px-8 py-4 rounded-2xl text-sm font-bold hover:bg-slate-800 transition-all shadow-xl shadow-slate-200 active:scale-95">
                <i data-lucide="download" class="w-5 h-5"></i> Download Full Dataset
            </button>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/50">
                        <th class="px-10 py-6 text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em]">Fiscal Year</th>
                        <th class="px-10 py-6 text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em]">Category</th>
                        <th class="px-10 py-6 text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em]">Usage Context</th>
                        <th class="px-10 py-6 text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em] text-right">Amount (USD)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    <?php foreach ($FINANCIAL_DATA as $item): ?>
                        <tr class="hover:bg-slate-50/50 transition-colors group">
                            <td class="px-10 py-6">
                                <span class="font-bold text-slate-900"><?php echo $item['year']; ?></span>
                            </td>
                            <td class="px-10 py-6">
                                <div class="flex items-center gap-2">
                                    <div class="w-2 h-2 rounded-full <?php echo $item['category'] === 'Programs' ? 'bg-emerald-500' : ($item['category'] === 'Fundraising' ? 'bg-blue-500' : 'bg-slate-400'); ?>"></div>
                                    <span class="text-sm font-bold text-slate-700"><?php echo $item['category']; ?></span>
                                </div>
                            </td>
                            <td class="px-10 py-6">
                                <span class="text-sm text-slate-500">
                                    <?php echo $item['usage_context']; ?>
                                </span>
                            </td>
                            <td class="px-10 py-6 text-right">
                                <span class="font-mono font-bold text-slate-900 text-lg">₦<?php echo number_format($item['amount']); ?></span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </section>

    <!-- Donation History Section -->
    <section class="bg-white rounded-[3rem] shadow-xl shadow-slate-200/40 border border-slate-100 overflow-hidden">
        <div class="p-8 md:p-12 border-b border-slate-50 flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div>
                <h2 class="text-2xl font-bold text-slate-900 flex items-center gap-3">
                    <i data-lucide="history" class="text-emerald-500 w-6 h-6"></i> Recent Contributions
                </h2>
                <p class="text-slate-500 mt-1">Real-time verification of community support and participation.</p>
            </div>
            <div class="inline-flex items-center gap-2 bg-emerald-50 text-emerald-600 px-6 py-3 rounded-2xl text-xs font-bold uppercase tracking-wider border border-emerald-100 shadow-sm">
                <i data-lucide="check-circle-2" class="w-4.5 h-4.5"></i> Verified Transactions
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/50">
                        <th class="px-10 py-6 text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em]">Donor Profile</th>
                        <th class="px-10 py-6 text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em]">Target Program</th>
                        <th class="px-10 py-6 text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em]">Submission Date</th>
                        <th class="px-10 py-6 text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em] text-right">Amount</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    <?php foreach ($MOCK_DONATION_HISTORY as $donation): ?>
                        <tr class="hover:bg-slate-50/50 transition-colors group">
                            <td class="px-10 py-6">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-2xl flex items-center justify-center <?php echo $donation['isAnonymous'] ? 'bg-slate-100 text-slate-400' : 'bg-emerald-50 text-emerald-600'; ?> shadow-sm border border-white">
                                        <i data-lucide="user" class="w-4.5 h-4.5"></i>
                                    </div>
                                    <span class="font-bold text-sm <?php echo $donation['isAnonymous'] ? 'text-slate-400 italic font-medium' : 'text-slate-700'; ?>">
                                        <?php echo $donation['isAnonymous'] ? 'Restricted Identity' : $donation['donorName']; ?>
                                    </span>
                                </div>
                            </td>
                            <td class="px-10 py-6">
                                <span class="text-xs font-bold text-slate-500 uppercase tracking-widest bg-slate-100 px-3 py-1 rounded-full"><?php echo $donation['campaignTitle']; ?></span>
                            </td>
                            <td class="px-10 py-6">
                                <span class="text-sm text-slate-400 font-medium">
                                    <?php echo date('M j, Y', strtotime($donation['date'])); ?>
                                </span>
                            </td>
                            <td class="px-10 py-6 text-right">
                                <span class="text-lg font-bold text-slate-900">₦<?php echo number_format($donation['amount']); ?></span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </section>
</div>

<script>
    // Prepare data for Chart.js
    const labels = <?php echo json_encode(array_column($currentYearData, 'category')); ?>;
    const values = <?php echo json_encode(array_column($currentYearData, 'amount')); ?>;
    const emeraldColors = ['#10b981', '#059669', '#34d399', '#6ee7b7', '#a7f3d0'];

    // Pie Chart
    const pieCtx = document.getElementById('pieChart').getContext('2d');
    new Chart(pieCtx, {
        type: 'doughnut',
        data: {
            labels: labels,
            datasets: [{
                data: values,
                backgroundColor: emeraldColors,
                borderWidth: 0,
                hoverOffset: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '70%',
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#fff',
                    titleColor: '#1e293b',
                    bodyColor: '#64748b',
                    padding: 12,
                    cornerRadius: 16,
                    displayColors: false,
                    callbacks: {
                        label: function(context) {
                            return '$' + context.raw.toLocaleString();
                        }
                    }
                }
            }
        }
    });

    // Bar Chart
    const barCtx = document.getElementById('barChart').getContext('2d');
    new Chart(barCtx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                data: values,
                backgroundColor: '#10b981',
                borderRadius: 8
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { display: false },
                    ticks: {
                        callback: function(value) { return '$' + value.toLocaleString(); },
                        font: { size: 10, weight: '600' }
                    }
                },
                x: {
                    grid: { display: false },
                    ticks: { font: { size: 10, weight: '600' } }
                }
            },
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#fff',
                    titleColor: '#1e293b',
                    bodyColor: '#64748b',
                    padding: 12,
                    cornerRadius: 16,
                    displayColors: false
                }
            }
        }
    });
</script>

<?php include_once 'includes/footer.php'; ?>
