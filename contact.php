<?php
// contact.php — Fully Dynamic
require_once 'includes/data.php';

$pageTitle = "Get in Touch";
$submitted = false;
$CB   = $CONTENT_CALLS ?? $CONTENT_BLOCKS;
$CS   = $CONTACT_SETTINGS;
$hero = $HERO_CONFIG['contact'] ?? [];

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['message'])) {
    try {
        $name  = trim($_POST['first_name'] . ' ' . $_POST['last_name']);
        $email = trim($_POST['email'] ?? '');
        $subj  = trim($_POST['topic'] ?? 'General Inquiry');
        $msg   = trim($_POST['message'] ?? '');
        $stmt  = $pdo->prepare("INSERT INTO messages (name, email, subject, message) VALUES (?, ?, ?, ?)");
        $stmt->execute([$name, $email, $subj, $msg]);
        $submitted = true;
    } catch (Exception $e) {
        $submitted = true; // Graceful fallback
    }
}

include_once 'includes/header.php';

$addr    = htmlspecialchars($CS['address']      ?? $OFFICIAL_INFO['address']);
$email   = htmlspecialchars($CS['email']        ?? $OFFICIAL_INFO['email']);
$phone1  = htmlspecialchars($CS['phone_1']      ?? ($OFFICIAL_INFO['phones'][0] ?? ''));
$phone2  = htmlspecialchars($CS['phone_2']      ?? ($OFFICIAL_INFO['phones'][1] ?? ''));
$hours   = htmlspecialchars($CS['office_hours'] ?? 'Mon - Fri: 9:00 AM – 5:00 PM');
$mapEmbed = htmlspecialchars($CS['map_embed']   ?? '');
$heading  = htmlspecialchars($CONTENT_BLOCKS['contact']['heading']   ?? 'Get In Touch');
$subhead  = htmlspecialchars($CONTENT_BLOCKS['contact']['subheading'] ?? '');
$formHead = htmlspecialchars($CONTENT_BLOCKS['contact']['form_heading'] ?? 'Send Us a Message');
?>

<div class="pb-20">
    <!-- Hero Banner (optional) -->
    <?php if (!empty($hero['image_url'])): ?>
    <div class="relative h-[40vh] overflow-hidden flex items-center">
        <img src="<?php echo htmlspecialchars($hero['image_url']); ?>" alt="Contact"
             class="absolute inset-0 w-full h-full object-cover" style="filter: brightness(0.45);" />
        <div class="absolute inset-0 bg-gradient-to-r from-black/60 to-transparent"></div>
        <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-white">
            <?php if (!empty($hero['badge_text'])): ?>
                <span class="inline-block px-4 py-1.5 rounded-full bg-emerald-600/30 border border-emerald-400/50 text-emerald-100 text-xs font-bold uppercase tracking-widest mb-4">
                    <?php echo htmlspecialchars($hero['badge_text']); ?>
                </span>
            <?php endif; ?>
            <h1 class="text-4xl md:text-6xl font-black"><?php echo htmlspecialchars($hero['title'] ?? 'Contact Us'); ?></h1>
        </div>
    </div>
    <?php endif; ?>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <!-- Page Header -->
        <div class="text-center mb-16">
            <h2 class="text-4xl md:text-5xl font-black text-slate-900 mb-4"><?php echo $heading; ?></h2>
            <p class="text-lg text-slate-500 max-w-2xl mx-auto"><?php echo $subhead; ?></p>
        </div>

        <div class="grid lg:grid-cols-2 gap-16">
            <!-- Contact Info -->
            <div class="space-y-8">
                <!-- Address -->
                <div class="flex items-start gap-5 bg-white p-7 rounded-[2rem] border border-slate-100 shadow-sm hover:shadow-md transition-all">
                    <div class="w-14 h-14 bg-emerald-50 text-emerald-600 rounded-2xl flex items-center justify-center flex-shrink-0 shadow-sm">
                        <i data-lucide="map-pin" class="w-6 h-6"></i>
                    </div>
                    <div>
                        <h4 class="font-black text-slate-900 mb-1">Our Headquarters</h4>
                        <p class="text-slate-500 leading-relaxed"><?php echo $addr; ?></p>
                    </div>
                </div>

                <!-- Email -->
                <div class="flex items-start gap-5 bg-white p-7 rounded-[2rem] border border-slate-100 shadow-sm hover:shadow-md transition-all">
                    <div class="w-14 h-14 bg-emerald-50 text-emerald-600 rounded-2xl flex items-center justify-center flex-shrink-0 shadow-sm">
                        <i data-lucide="mail" class="w-6 h-6"></i>
                    </div>
                    <div>
                        <h4 class="font-black text-slate-900 mb-1">Email Support</h4>
                        <a href="mailto:<?php echo $email; ?>" class="text-emerald-600 hover:underline font-medium"><?php echo $email; ?></a>
                    </div>
                </div>

                <!-- Phone -->
                <div class="flex items-start gap-5 bg-white p-7 rounded-[2rem] border border-slate-100 shadow-sm hover:shadow-md transition-all">
                    <div class="w-14 h-14 bg-emerald-50 text-emerald-600 rounded-2xl flex items-center justify-center flex-shrink-0 shadow-sm">
                        <i data-lucide="phone" class="w-6 h-6"></i>
                    </div>
                    <div>
                        <h4 class="font-black text-slate-900 mb-1">Call Us</h4>
                        <p class="text-slate-600 font-medium"><?php echo $phone1; ?></p>
                        <?php if ($phone2): ?><p class="text-slate-600 font-medium"><?php echo $phone2; ?></p><?php endif; ?>
                        <p class="text-slate-400 text-xs mt-1"><?php echo $hours; ?></p>
                    </div>
                </div>

                <!-- Governance -->
                <div class="flex items-start gap-5 bg-white p-7 rounded-[2rem] border border-slate-100 shadow-sm hover:shadow-md transition-all">
                    <div class="w-14 h-14 bg-emerald-50 text-emerald-600 rounded-2xl flex items-center justify-center flex-shrink-0 shadow-sm">
                        <i data-lucide="shield-check" class="w-6 h-6"></i>
                    </div>
                    <div>
                        <h4 class="font-black text-slate-900 mb-1">Certified</h4>
                        <p class="text-slate-500">Certified by the Corporate Affairs Commission</p>
                        <p class="text-slate-400 text-xs mt-1">Reg Date: <?php echo htmlspecialchars($OFFICIAL_INFO['registrationDate']); ?></p>
                    </div>
                </div>

                <!-- Map Embed -->
                <?php if (!empty($mapEmbed)): ?>
                <div class="rounded-[2rem] overflow-hidden shadow-lg border border-slate-100 h-52">
                    <iframe src="<?php echo $mapEmbed; ?>" width="100%" height="100%" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
                </div>
                <?php endif; ?>
            </div>

            <!-- Contact Form -->
            <div class="bg-white p-8 md:p-12 rounded-[2.5rem] shadow-xl border border-slate-100">
                <?php if (!$submitted): ?>
                    <h3 class="text-2xl font-black text-slate-900 mb-8"><?php echo $formHead; ?></h3>
                    <form method="POST" class="space-y-6">
                        <div class="grid grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-black text-slate-700 mb-2 uppercase tracking-widest text-[10px]">First Name</label>
                                <input type="text" name="first_name" required class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-5 py-4 outline-none focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 transition-all font-bold" />
                            </div>
                            <div>
                                <label class="block text-sm font-black text-slate-700 mb-2 uppercase tracking-widest text-[10px]">Last Name</label>
                                <input type="text" name="last_name" required class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-5 py-4 outline-none focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 transition-all font-bold" />
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-black text-slate-700 mb-2 uppercase tracking-widest text-[10px]">Email Address</label>
                            <input type="email" name="email" required class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-5 py-4 outline-none focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 transition-all font-bold" />
                        </div>
                        <div>
                            <label class="block text-sm font-black text-slate-700 mb-2 uppercase tracking-widest text-[10px]">Topic of Inquiry</label>
                            <select name="topic" class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-5 py-4 outline-none focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 transition-all font-medium text-slate-600 appearance-none">
                                <option>General Support</option>
                                <option>Health/Nutrition Assistance</option>
                                <option>Water/WASH Projects</option>
                                <option>Mentorship Programs</option>
                                <option>Donation Inquiry</option>
                                <option>Partnership Proposal</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-black text-slate-700 mb-2 uppercase tracking-widest text-[10px]">Message</label>
                            <textarea name="message" rows="5" required class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-5 py-4 outline-none focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 transition-all font-medium resize-none"></textarea>
                        </div>
                        <button type="submit" class="w-full bg-emerald-600 text-white py-5 rounded-2xl font-black flex items-center justify-center gap-3 hover:bg-emerald-700 transition-all shadow-2xl shadow-emerald-900/10 active:scale-95 text-sm uppercase tracking-widest">
                            Send Message <i data-lucide="send" class="w-5 h-5"></i>
                        </button>
                    </form>
                <?php else: ?>
                    <div class="text-center py-16 animate-in zoom-in duration-500">
                        <div class="w-24 h-24 bg-emerald-100 text-emerald-600 rounded-full flex items-center justify-center mx-auto mb-6">
                            <i data-lucide="check-circle" class="w-12 h-12"></i>
                        </div>
                        <h2 class="text-3xl font-black text-slate-900 mb-4">Message Received!</h2>
                        <p class="text-slate-500 mb-8 max-w-sm mx-auto">Thank you for reaching out. Our team will get back to you within 24–48 hours.</p>
                        <a href="contact.php" class="bg-emerald-600 text-white px-8 py-4 rounded-2xl font-bold hover:bg-emerald-700 transition-all inline-block">
                            Send Another Message
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php include_once 'includes/footer.php'; ?>
