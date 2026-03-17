
<?php
// includes/navbar.php

// Start session safely
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$currentPage = basename($_SERVER['PHP_SELF'], ".php");
if ($currentPage == "index") $currentPage = "home";

// Intelligent path highlighting
$activeParent = $currentPage;
if (strpos($currentPage, 'campaign') !== false) $activeParent = 'campaigns';
if (strpos($currentPage, 'blog') !== false) $activeParent = 'blogs';
if (strpos($currentPage, 'transparency') !== false) $activeParent = 'transparency';

// Default navigation links
$navLinks = [
    ['name' => 'Home', 'id' => 'home', 'url' => 'index.php', 'icon' => 'home'],
    ['name' => 'About', 'id' => 'about', 'url' => 'about.php', 'icon' => 'info'],
    ['name' => 'Campaigns', 'id' => 'campaigns', 'url' => 'campaigns.php', 'icon' => 'megaphone'],
    ['name' => 'Transparency', 'id' => 'transparency', 'url' => 'transparency.php', 'icon' => 'pie-chart'],
    ['name' => 'Contact', 'id' => 'contact', 'url' => 'contact.php', 'icon' => 'mail'],
];

// Detect user/admin session
$user = null;
$isAdmin = false;

if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
    $isAdmin = true;
}

if (isset($_SESSION['is_user_logged_in']) && $_SESSION['is_user_logged_in'] === true) {
    $user = [
        'name' => $_SESSION['user_name'],
        'email' => $_SESSION['user_email']
    ];

    // Add profile link for logged in users
    $navLinks[] = [
        'name' => 'Profile',
        'id' => 'profile',
        'url' => 'profile.php',
        'icon' => 'user'
    ];
}
?>

<nav id="navbar" class="sticky top-0 z-50 transition-all duration-300 bg-white/80 backdrop-blur-xl border-b border-slate-100/50">
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

<div class="flex justify-between h-20 items-center">

<!-- Logo -->
<a href="index.php" class="flex items-center gap-3 group shrink-0">

<div class="relative">
<div class="absolute inset-0 bg-emerald-400 rounded-2xl blur-xl opacity-0 group-hover:opacity-30 transition-opacity"></div>

<div class="relative w-11 h-11 text-white bg-emerald-600 rounded-2xl flex items-center justify-center shadow-lg shadow-emerald-200 group-hover:scale-105 group-hover:rotate-3 transition-all duration-300">
<i data-lucide="heart" class="w-6 h-6 fill-white"></i>
</div>
</div>

<div class="flex flex-col">
<span class="font-bold text-xl tracking-tight text-slate-900 leading-none">
Al-Shifah
</span>
<span class="text-[9px] text-emerald-600 font-bold uppercase tracking-[0.2em] mt-1 opacity-80">
Foundation
</span>
</div>

</a>

<!-- Desktop Navigation -->
<div class="hidden lg:flex items-center gap-1">

<?php foreach ($navLinks as $link): ?>

<a href="<?php echo $link['url']; ?>"
class="relative px-5 py-2 rounded-xl text-sm font-semibold transition-all duration-300 flex items-center gap-2
<?php echo $activeParent === $link['id'] ? 'text-emerald-700 bg-emerald-50' : 'text-slate-500 hover:text-emerald-600 hover:bg-slate-50'; ?>">

<i data-lucide="<?php echo $link['icon']; ?>" class="w-4 h-4
<?php echo $activeParent === $link['id'] ? 'text-emerald-600' : 'opacity-0 group-hover:opacity-100 transition-opacity'; ?>"></i>

<?php echo $link['name']; ?>

</a>

<?php endforeach; ?>

</div>

<!-- Action Area -->
<div class="hidden lg:flex items-center gap-4 shrink-0">

<a href="donate.php"
class="relative overflow-hidden bg-emerald-600 text-white px-7 py-3 rounded-2xl text-sm font-bold hover:bg-emerald-700 hover:shadow-xl hover:shadow-emerald-100 transition-all duration-300 active:scale-95 group">

<span class="relative z-10 flex items-center gap-2">
<i data-lucide="heart-handshake" class="w-4 h-4"></i>
Donate Now
</span>

<div class="absolute inset-0 bg-gradient-to-r from-emerald-600 to-emerald-500 translate-x-full group-hover:translate-x-0 transition-transform duration-500"></div>

</a>

<div class="h-8 w-px bg-slate-200 mx-1"></div>

<?php if ($user || $isAdmin): ?>

<div class="relative group">

<button class="flex items-center gap-3 p-1.5 pr-4 rounded-2xl hover:bg-slate-50 transition-all border border-transparent hover:border-slate-100">

<div class="w-9 h-9 rounded-xl bg-slate-900 flex items-center justify-center text-white font-bold shadow-lg shadow-slate-200">
<?php echo strtoupper(substr(($user['name'] ?? 'A'), 0, 1)); ?>
</div>

<div class="text-left">
<p class="text-[11px] font-bold text-slate-900 leading-tight">
<?php echo htmlspecialchars($user['name'] ?? 'Admin'); ?>
</p>

<p class="text-[9px] text-emerald-600 font-bold uppercase tracking-tighter">
Account
</p>
</div>

<i data-lucide="chevron-down" class="w-4 h-4 text-slate-400 group-hover:rotate-180 transition-transform duration-300"></i>

</button>

<!-- Dropdown -->
<div class="absolute right-0 top-full pt-3 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-300 translate-y-2 group-hover:translate-y-0 z-50">

<div class="w-56 bg-white rounded-3xl shadow-[0_20px_50px_rgba(0,0,0,0.1)] border border-slate-100 p-2">

<div class="px-4 py-3 mb-2 bg-slate-50/50 rounded-2xl">

<p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">
Logged in as
</p>

<p class="text-sm font-bold text-slate-900 truncate">
<?php echo htmlspecialchars($user['email'] ?? 'Administrator'); ?>
</p>

</div>

<?php if ($isAdmin): ?>

<a href="admin.php"
class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold text-slate-700 hover:bg-emerald-50 hover:text-emerald-700 transition-colors">

<i data-lucide="shield" class="w-4.5 h-4.5"></i>
Admin Dashboard

</a>

<?php endif; ?>

<?php if ($user): ?>

<a href="profile.php"
class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold text-slate-700 hover:bg-emerald-50 hover:text-emerald-700 transition-colors">

<i data-lucide="user-circle" class="w-4.5 h-4.5"></i>
My Profile

</a>

<?php endif; ?>

<div class="my-1 border-t border-slate-50"></div>

<a href="logout.php"
class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold text-red-500 hover:bg-red-50 transition-colors">

<i data-lucide="log-out" class="w-4.5 h-4.5"></i>
Logout

</a>

</div>
</div>
</div>

<?php else: ?>

<div class="flex items-center gap-2">

<a href="login.php"
class="px-5 py-2 text-sm font-semibold text-slate-600 hover:text-emerald-600 transition-colors">
Login
</a>

<a href="signup.php"
class="px-6 py-2.5 bg-slate-900 text-white text-sm font-bold rounded-2xl hover:bg-emerald-600 hover:shadow-xl hover:shadow-emerald-100 transition-all active:scale-95">
Signup
</a>

</div>

<?php endif; ?>

</div>

</div>
</div>
</nav>

<script>

// Navbar shadow on scroll
window.addEventListener('scroll', () => {

const navbar = document.getElementById('navbar');

if (window.scrollY > 20) {

navbar.classList.add('shadow-xl','shadow-slate-200/50');
navbar.classList.replace('bg-white/80','bg-white/95');

} else {

navbar.classList.remove('shadow-xl','shadow-slate-200/50');
navbar.classList.replace('bg-white/95','bg-white/80');

}

});

// Initialize Lucide Icons
lucide.createIcons();

</script>

