<?php
// login.php
session_start();
require_once 'includes/data.php';

$pageTitle = "Login - Al-Shifah";
$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $identity = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');

    // 1. Check in admins table (accepts either username or email)
    $stmt = $pdo->prepare("SELECT * FROM admins WHERE email = ? OR username = ?");
    $stmt->execute([$identity, $identity]);
    $admin = $stmt->fetch();

    if ($admin && password_verify($password, $admin['password'])) {
        $_SESSION['admin_id'] = $admin['id'];
        $_SESSION['admin_username'] = $admin['username'];
        $_SESSION['logged_in'] = true;
        
        header("Location: admin.php");
        exit;
    } 

    // 2. Check in regular users table (email only)
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$identity]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['full_name'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['is_user_logged_in'] = true;

        header("Location: index.php");
        exit;
    }

    $error = "Invalid credentials. Please try again.";
}

include_once 'includes/header.php';
?>

<div class="min-h-[80vh] flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8 bg-white p-10 rounded-[2.5rem] shadow-2xl shadow-slate-200/50 border border-slate-100 animate-in fade-in zoom-in duration-500">
        <div class="text-center">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-emerald-50 text-emerald-600 rounded-3xl mb-4">
                <i data-lucide="shield-check" class="w-8 h-8"></i>
            </div>
            <h2 class="text-3xl font-bold text-slate-900 tracking-tight">Welcome Back</h2>
            <p class="mt-2 text-slate-500 font-medium">Sign in to manage the foundation.</p>
        </div>

        <?php if ($error): ?>
            <div class="bg-red-50 text-red-600 p-4 rounded-2xl border border-red-100 text-sm font-bold animate-pulse">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>
        
        <form class="mt-8 space-y-6" method="POST">
            <div class="space-y-4">
                <div class="relative">
                    <i data-lucide="mail" class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 w-5 h-5"></i>
                    <input
                        type="text"
                        name="email"
                        required
                        placeholder="Username or Email"
                        class="w-full bg-slate-50 border-2 border-slate-100 rounded-2xl py-4 pl-12 pr-4 font-medium outline-none focus:border-emerald-600 transition-all"
                    />
                </div>
                <div class="relative">
                    <i data-lucide="lock" class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 w-5 h-5"></i>
                    <input
                        type="password"
                        name="password"
                        required
                        placeholder="Password"
                        class="w-full bg-slate-50 border-2 border-slate-100 rounded-2xl py-4 pl-12 pr-4 font-medium outline-none focus:border-emerald-600 transition-all"
                    />
                </div>
            </div>

            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <input id="remember-me" name="remember" type="checkbox" class="h-4 w-4 text-emerald-600 focus:ring-emerald-500 border-slate-300 rounded" />
                    <label htmlFor="remember-me" class="ml-2 block text-sm text-slate-500 font-medium">Remember me</label>
                </div>
                <button type="button" class="text-sm font-bold text-emerald-600 hover:text-emerald-700">Forgot password?</button>
            </div>

            <div class="space-y-3">
                <button
                    type="submit"
                    class="group relative w-full flex justify-center py-4 px-4 border border-transparent font-bold rounded-2xl text-white bg-emerald-600 hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 shadow-lg shadow-emerald-200 transition-all active:scale-95"
                >
                    Sign In <i data-lucide="arrow-right" class="ml-2 group-hover:translate-x-1 transition-transform w-5 h-5"></i>
                </button>
                
                <a
                    href="donate.php"
                    class="w-full flex justify-center py-4 px-4 border-2 border-slate-100 font-bold rounded-2xl text-slate-600 bg-white hover:bg-slate-50 transition-all"
                >
                    Continue as Guest
                </a>
            </div>
        </form>

        <div class="text-center pt-4 border-t border-slate-100">
            <p class="text-sm text-slate-500 font-medium">
                Don't have an account?{' '}
                <a
                    href="signup.php"
                    class="text-emerald-600 font-bold hover:underline"
                >
                    Sign Up
                </a>
            </p>
        </div>
    </div>
</div>

<?php include_once 'includes/footer.php'; ?>
