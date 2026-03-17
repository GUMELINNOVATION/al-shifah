<?php
// includes/footer.php
require_once 'data.php';
?>

</main> <!-- End of main content from header.php -->

<footer class="bg-slate-900 text-slate-300 py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-12 lg:gap-8">
            <div class="space-y-6">
                <h3 class="text-white font-bold text-xl uppercase tracking-wider flex items-center gap-2">
                    <div class="w-8 h-8 bg-emerald-500 rounded-lg flex items-center justify-center">
                        <i data-lucide="send" class="w-4 h-4 text-white fill-white"></i>
                    </div>
                    <?php echo getSetting('site_name', $pdo) ?: 'Al-Shifah'; ?>
                </h3>
                <p class="text-sm leading-relaxed text-slate-400">
                    Establishing services within the community and making a real difference through compassionate support and sustainable programs.
                </p>
                <div class="flex space-x-4">
                    <i data-lucide="facebook" class="cursor-pointer hover:text-emerald-400 transition-colors w-5 h-5"></i>
                    <i data-lucide="twitter" class="cursor-pointer hover:text-emerald-400 transition-colors w-5 h-5"></i>
                    <i data-lucide="instagram" class="cursor-pointer hover:text-pink-400 transition-colors w-5 h-5"></i>
                    <i data-lucide="linkedin" class="cursor-pointer hover:text-emerald-500 transition-colors w-5 h-5"></i>
                </div>
            </div>
            
            <div>
                <h4 class="text-white font-semibold mb-6 uppercase tracking-widest text-xs">Quick Links</h4>
                <ul class="space-y-3 text-sm">
                    <li><a href="about.php" class="hover:text-emerald-400 transition-colors">Our Mission</a></li>
                    <li><a href="about.php#aims" class="hover:text-emerald-400 transition-colors">Aims & Objectives</a></li>
                    <li><a href="about.php#team" class="hover:text-emerald-400 transition-colors">Governing Body</a></li>
                    <li><a href="transparency.php" class="hover:text-emerald-400 transition-colors">Transparency Reports</a></li>
                    <li><a href="index.php#blog" class="hover:text-emerald-400 transition-colors">Latest News</a></li>
                </ul>
            </div>

            <div>
                <h4 class="text-white font-semibold mb-6 uppercase tracking-widest text-xs">Official Contact</h4>
                <ul class="space-y-4 text-sm">
                    <li class="flex items-start gap-3">
                        <i data-lucide="map-pin" class="w-4.5 h-4.5 text-emerald-400 mt-0.5 flex-shrink-0"></i>
                        <span class="leading-relaxed text-slate-400"><?php echo $OFFICIAL_INFO['address']; ?></span>
                    </li>
                    <li class="flex items-center gap-3">
                        <i data-lucide="phone" class="w-4.5 h-4.5 text-emerald-400 flex-shrink-0"></i>
                        <span class="text-slate-400"><?php echo $OFFICIAL_INFO['phones'][0]; ?></span>
                    </li>
                    <li class="flex items-center gap-3">
                        <i data-lucide="mail" class="w-4.5 h-4.5 text-emerald-400 flex-shrink-0"></i>
                        <a href="mailto:<?php echo $OFFICIAL_INFO['email']; ?>" class="text-slate-400 hover:text-emerald-400 transition-colors truncate"><?php echo $OFFICIAL_INFO['email']; ?></a>
                    </li>
                </ul>
            </div>

            <div class="space-y-6">
                <h4 class="text-white font-semibold mb-2 uppercase tracking-widest text-xs">Stay Updated</h4>
                <p class="text-sm text-slate-400">Subscribe to receive field updates, activity reports, and impact stories directly.</p>
                
                <form id="newsletter-form" class="space-y-3">
                    <div class="relative">
                        <input 
                            type="email" 
                            required 
                            placeholder="your@email.com" 
                            class="w-full bg-white/5 border border-white/10 rounded-xl py-3 px-4 text-sm text-white focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition-all"
                        />
                    </div>
                    <button 
                        type="submit" 
                        class="w-full bg-emerald-600 hover:bg-emerald-500 text-white font-bold py-3 rounded-xl text-sm transition-all shadow-lg shadow-emerald-900/20 active:scale-95 flex items-center justify-center gap-2"
                    >
                        Subscribe
                    </button>
                    <div id="subscription-success" class="hidden bg-emerald-500/10 border border-emerald-500/20 p-4 rounded-xl flex items-center gap-3 animate-in fade-in zoom-in duration-300">
                        <i data-lucide="check-circle-2" class="text-emerald-500 flex-shrink-0 w-5 h-5"></i>
                        <p class="text-emerald-400 text-xs font-bold leading-tight uppercase tracking-wider">You're on the list! Thank you.</p>
                    </div>
                </form>
                <p class="text-[10px] text-slate-500 italic">By subscribing, you agree to receive humanitarian updates from Al-Shifah.</p>
            </div>
        </div>
        
        <div class="mt-16 pt-8 border-t border-slate-800 flex flex-col md:flex-row justify-between items-center gap-4 text-[10px] text-slate-500">
            <div class="flex items-center gap-4">
                <p class="font-bold text-slate-400 uppercase tracking-widest">CAC Reg Date: <?php echo $OFFICIAL_INFO['registrationDate']; ?></p>
                <span class="hidden md:inline">•</span>
                <p>&copy; <?php echo date("Y"); ?> AL-SHIFAH CHARITY FOUNDATION</p>
            </div>
            <div class="flex gap-6">
                <a href="#" class="hover:text-white transition-colors">Privacy Policy</a>
                <a href="#" class="hover:text-white transition-colors">Terms of Service</a>
                <a href="#" class="hover:text-white transition-colors">Audit Disclosure</a>
            </div>
        </div>
    </div>
</footer>

<script>
    // Newsletter form handled with JS
    const newsletterForm = document.getElementById('newsletter-form');
    const subscriptionSuccess = document.getElementById('subscription-success');

    if (newsletterForm) {
        newsletterForm.addEventListener('submit', (e) => {
            e.preventDefault();
            const button = newsletterForm.querySelector('button');
            const input = newsletterForm.querySelector('input');
            
            button.classList.add('hidden');
            input.classList.add('hidden');
            subscriptionSuccess.classList.remove('hidden');
            
            setTimeout(() => {
                subscriptionSuccess.classList.add('hidden');
                button.classList.remove('hidden');
                input.classList.remove('hidden');
                input.value = '';
                lucide.createIcons();
            }, 5000);
        });
    }

    // Refresh icons in case they were hidden
    lucide.createIcons();
</script>
</body>
</html>
