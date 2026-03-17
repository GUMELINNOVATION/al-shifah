<?php
// profile.php
session_start();
require_once 'includes/data.php';

// Redirect if not logged in
if (!isset($_SESSION['is_user_logged_in']) || $_SESSION['is_user_logged_in'] !== true) {
    header("Location: login.php");
    exit;
}

$pageTitle = "My Profile - Al-Shifah";
$userId = $_SESSION['user_id'];

// Fetch user's personal donation history
$stmt = $pdo->prepare("SELECT d.*, c.title as campaignTitle FROM donations d LEFT JOIN campaigns c ON d.campaign_id = c.id WHERE d.user_id = ? ORDER BY d.donation_date DESC");
$stmt->execute([$userId]);
$userDonations = $stmt->fetchAll();

$totalDonated = array_reduce($userDonations, function($sum, $d) {
    return $sum + $d['amount'];
}, 0);

$donationCount = count($userDonations);

// Find most supported category
$categories = [];
foreach ($userDonations as $d) {
    $stmtCategory = $pdo->prepare("SELECT category FROM campaigns WHERE id = ?");
    $stmtCategory->execute([$d['campaign_id']]);
    $cat = $stmtCategory->fetchColumn();
    if ($cat) {
        $categories[$cat] = ($categories[$cat] ?? 0) + 1;
    }
}
arsort($categories);
$topCategory = !empty($categories) ? array_key_first($categories) : 'General';

include_once 'includes/header.php';
?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 space-y-12">
    <!-- Profile Header -->
    <section class="bg-white rounded-[3rem] shadow-xl shadow-slate-200/40 border border-slate-100 p-8 md:p-12 relative overflow-hidden">
        <div class="absolute top-0 right-0 w-64 h-64 bg-emerald-50 rounded-full -mr-20 -mt-20 blur-3xl opacity-50"></div>
        <div class="relative z-10 flex flex-col md:flex-row items-center gap-12">
            <div class="w-32 h-32 bg-gradient-to-br from-emerald-400 to-emerald-600 rounded-[3rem] flex items-center justify-center text-white shadow-xl shadow-emerald-200 ring-8 ring-emerald-50">
                <i data-lucide="user" class="w-16 h-16"></i>
            </div>
            <div class="text-center md:text-left flex-1">
                <div class="flex flex-col md:flex-row md:items-center gap-4 mb-4">
                    <h1 class="text-4xl font-black text-slate-900 tracking-tight"><?php echo htmlspecialchars($_SESSION['user_name']); ?></h1>
                    <div class="flex gap-2 justify-center md:justify-start">
                        <span class="px-4 py-1.5 rounded-full bg-emerald-100 text-emerald-700 text-[10px] font-bold uppercase tracking-widest">Active Donor</span>
                        <span class="px-4 py-1.5 rounded-full bg-slate-100 text-slate-600 text-[10px] font-bold uppercase tracking-widest">Member since <?php echo date('Y'); ?></span>
                    </div>
                </div>
                <p class="text-xl text-slate-500 font-medium mb-6"><?php echo htmlspecialchars($_SESSION['user_email']); ?></p>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                    <div class="bg-slate-50 p-6 rounded-3xl border border-slate-100">
                        <div class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2">Total Contribution</div>
                        <div class="text-2xl font-black text-slate-900">₦<?php echo number_format($totalDonated); ?></div>
                    </div>
                    <div class="bg-slate-50 p-6 rounded-3xl border border-slate-100">
                        <div class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2">Contributions</div>
                        <div class="text-2xl font-black text-slate-900"><?php echo $donationCount; ?></div>
                    </div>
                    <div class="bg-slate-50 p-6 rounded-3xl border border-slate-100">
                        <div class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2">Top Cause</div>
                        <div class="text-2xl font-black text-emerald-600"><?php echo htmlspecialchars($topCategory); ?></div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Contribution History -->
    <section class="bg-white rounded-[3rem] shadow-xl shadow-slate-200/40 border border-slate-100 overflow-hidden">
        <div class="p-8 md:p-12 border-b border-slate-50 flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold text-slate-900">My Donation History</h2>
                <p class="text-slate-500 mt-1">A record of your compassionate support for our causes.</p>
            </div>
            <i data-lucide="history" class="w-8 h-8 text-emerald-500 opacity-20"></i>
        </div>

        <?php if (empty($userDonations)): ?>
            <div class="p-20 text-center">
                <div class="inline-flex items-center justify-center w-20 h-20 bg-slate-50 text-slate-300 rounded-full mb-6">
                    <i data-lucide="heart-off" class="w-10 h-10"></i>
                </div>
                <h3 class="text-xl font-bold text-slate-900 mb-2">No donations yet.</h3>
                <p class="text-slate-500 mb-8 max-w-sm mx-auto">Your contributions will appear here once you've supported one of our campaigns.</p>
                <a href="campaigns.php" class="inline-flex items-center gap-2 bg-emerald-600 text-white px-8 py-4 rounded-2xl font-bold hover:bg-emerald-700 transition-all shadow-lg shadow-emerald-200">
                    Browse Campaigns <i data-lucide="arrow-right" class="w-5 h-5"></i>
                </a>
            </div>
        <?php else: ?>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50/50">
                            <th class="px-10 py-6 text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em]">Campaign</th>
                            <th class="px-10 py-6 text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em]">Date</th>
                            <th class="px-10 py-6 text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em]">Privacy</th>
                            <th class="px-10 py-6 text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em] text-right">Amount</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        <?php foreach ($userDonations as $donation): ?>
                            <tr class="hover:bg-slate-50/50 transition-colors">
                                <td class="px-10 py-6">
                                    <span class="font-bold text-slate-900"><?php echo htmlspecialchars($donation['campaignTitle'] ?? 'General Fund'); ?></span>
                                </td>
                                <td class="px-10 py-6">
                                    <span class="text-sm font-medium text-slate-500"><?php echo date('M j, Y', strtotime($donation['donation_date'])); ?></span>
                                </td>
                                <td class="px-10 py-6">
                                    <?php if ($donation['is_anonymous']): ?>
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-slate-100 text-slate-500 text-[10px] font-bold uppercase tracking-wider">
                                            <i data-lucide="eye-off" class="w-3 h-3"></i> Anonymous
                                        </span>
                                    <?php else: ?>
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-50 text-emerald-600 text-[10px] font-bold uppercase tracking-wider">
                                            <i data-lucide="eye" class="w-3 h-3"></i> Public
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-10 py-6 text-right">
                                    <span class="text-xl font-bold text-slate-900">₦<?php echo number_format($donation['amount']); ?></span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </section>

    <!-- Profile Actions -->
    <div class="flex justify-between items-center bg-slate-50 p-6 rounded-[2rem] border border-slate-100">
        <div class="flex items-center gap-3 text-slate-500 text-sm font-medium">
            <i data-lucide="info" class="w-5 h-5 text-emerald-500"></i>
            Need to update your details? Contact support at <span class="text-emerald-600"><?php echo htmlspecialchars($OFFICIAL_INFO['email']); ?></span>
        </div>
        <a href="logout.php" class="flex items-center gap-2 text-red-500 font-bold hover:text-red-600 transition-colors">
            <i data-lucide="log-out" class="w-5 h-5"></i> Logout
        </a>
    </div>
</div>

<?php include_once 'includes/footer.php'; ?>
