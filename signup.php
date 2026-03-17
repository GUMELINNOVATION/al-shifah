<?php
// signup.php
session_start();
require_once 'includes/data.php';

$pageTitle = "Sign Up - Al-Shifah";
$error = "";
$success = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $full_name = trim($_POST['full_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $phone = trim($_POST['phone'] ?? '');

    if (empty($full_name) || empty($email) || empty($password)) {
        $error = "Please fill in all required fields.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format.";
    } else {
        // Check if user already exists
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $error = "An account with this email already exists.";
        } else {
            // Create user
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (full_name, email, password, phone) VALUES (?, ?, ?, ?)");
            if ($stmt->execute([$full_name, $email, $hashed_password, $phone])) {
                $success = "Account created successfully! You can now log in.";
                // Optionally log them in immediately
                // $_SESSION['user_id'] = $pdo->lastInsertId();
                // $_SESSION['user_name'] = $full_name;
                // $_SESSION['is_user_logged_in'] = true;
                // header("Location: index.php"); exit;
            } else {
                $error = "Something went wrong. Please try again.";
            }
        }
    }
}

include_once 'includes/header.php';
?>

<div class="min-h-[80vh] flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8 bg-white p-10 rounded-[2.5rem] shadow-2xl shadow-slate-200/50 border border-slate-100 animate-in fade-in zoom-in duration-500">
        <div class="text-center">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-emerald-50 text-emerald-600 rounded-3xl mb-4">
                <i data-lucide="user-plus" class="w-8 h-8"></i>
            </div>
            <h2 class="text-3xl font-bold text-slate-900 tracking-tight">Create Account</h2>
            <p class="mt-2 text-slate-500 font-medium">Join us in making a difference.</p>
        </div>

        <?php if ($error): ?>
            <div class="bg-red-50 text-red-600 p-4 rounded-2xl border border-red-100 text-sm font-bold animate-pulse">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="bg-emerald-50 text-emerald-600 p-4 rounded-2xl border border-emerald-100 text-sm font-bold">
                <?php echo $success; ?>
                <div class="mt-2">
                    <a href="login.php" class="underline">Click here to log in</a>
                </div>
            </div>
        <?php endif; ?>
        
        <form class="mt-8 space-y-6" method="POST">
            <div class="space-y-4">
                <div class="relative">
                    <i data-lucide="user" class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 w-5 h-5"></i>
                    <input
                        type="text"
                        name="full_name"
                        required
                        placeholder="Full Name"
                        value="<?php echo htmlspecialchars($_POST['full_name'] ?? ''); ?>"
                        class="w-full bg-slate-50 border-2 border-slate-100 rounded-2xl py-4 pl-12 pr-4 font-medium outline-none focus:border-emerald-600 transition-all"
                    />
                </div>
                <div class="relative">
                    <i data-lucide="mail" class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 w-5 h-5"></i>
                    <input
                        type="email"
                        name="email"
                        required
                        placeholder="Email Address"
                        value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>"
                        class="w-full bg-slate-50 border-2 border-slate-100 rounded-2xl py-4 pl-12 pr-4 font-medium outline-none focus:border-emerald-600 transition-all"
                    />
                </div>
                <div class="relative">
                    <i data-lucide="phone" class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 w-5 h-5"></i>
                    <input
                        type="text"
                        name="phone"
                        placeholder="Phone Number (Optional)"
                        value="<?php echo htmlspecialchars($_POST['phone'] ?? ''); ?>"
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

            <div class="space-y-3">
                <button
                    type="submit"
                    class="group relative w-full flex justify-center py-4 px-4 border border-transparent font-bold rounded-2xl text-white bg-emerald-600 hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 shadow-lg shadow-emerald-200 transition-all active:scale-95"
                >
                    Sign Up <i data-lucide="user-plus" class="ml-2 group-hover:translate-x-1 transition-transform w-5 h-5"></i>
                </button>
            </div>
        </form>

        <div class="text-center pt-4 border-t border-slate-100">
            <p class="text-sm text-slate-500 font-medium">
                Already have an account? 
                <a
                    href="login.php"
                    class="text-emerald-600 font-bold hover:underline"
                >
                    Log In
                </a>
            </p>
        </div>
    </div>
</div>

<?php include_once 'includes/footer.php'; ?>
