<?php
// donate.php
require_once 'includes/data.php';

$id = isset($_GET['campaign']) ? $_GET['campaign'] : null;
$selectedCampaign = getCampaignById($id, $CAMPAIGNS);

$pageTitle = "Support Al-Shifah";

include_once 'includes/header.php';
?>

<!-- Paystack Inline JS -->
<script src="https://js.paystack.co/v1/inline.js"></script>

<div class="min-h-[80vh] py-16 bg-slate-50">
    <div class="max-w-4xl mx-auto px-4">
        <!-- Progress Stepper -->
        <div class="flex justify-between items-center mb-12 max-w-xs mx-auto">
            <div class="step-indicator flex items-center" data-step="1">
                <div class="step-circle w-8 h-8 rounded-full flex items-center justify-center font-bold text-sm bg-emerald-600 text-white">1</div>
                <div class="step-line w-12 h-1 bg-slate-200"></div>
            </div>
            <div class="step-indicator flex items-center" data-step="2">
                <div class="step-circle w-8 h-8 rounded-full flex items-center justify-center font-bold text-sm bg-slate-200 text-slate-500">2</div>
                <div class="step-line w-12 h-1 bg-slate-200"></div>
            </div>
            <div class="step-indicator flex items-center" data-step="3">
                <div class="step-circle w-8 h-8 rounded-full flex items-center justify-center font-bold text-sm bg-slate-200 text-slate-500">3</div>
            </div>
        </div>

        <div class="bg-white rounded-3xl shadow-xl overflow-hidden grid md:grid-cols-5 min-h-[500px]">
            <!-- Form Side -->
            <div class="md:col-span-3 p-8 md:p-12">
                <!-- Step 1: Amount Selection -->
                <div id="step-1" class="animate-in fade-in slide-in-from-bottom duration-500">
                    <h2 class="text-3xl font-bold text-slate-900 mb-2">Support our Foundation</h2>
                    <p class="text-slate-500 mb-8">Select an amount to help Al-Shifah establish community services.</p>
                    
                    <div class="flex bg-slate-100 p-1 rounded-xl mb-8">
                        <button id="btn-one-time" class="flex-1 py-2 rounded-lg text-sm font-bold transition-all bg-white shadow text-emerald-600">One-time</button>
                        <button id="btn-monthly" class="flex-1 py-2 rounded-lg text-sm font-bold transition-all text-slate-500">Monthly</button>
                    </div>

                    <div class="grid grid-cols-3 gap-4 mb-8">
                        <?php $amounts = [10, 25, 50, 100, 250, 500]; ?>
                        <?php foreach ($amounts as $a): ?>
                            <button type="button" class="amount-btn py-4 rounded-xl border-2 font-bold text-lg transition-all border-slate-100 hover:border-slate-200 text-slate-600 <?php echo $a === 50 ? 'border-emerald-600 bg-emerald-50 text-emerald-600' : ''; ?>" data-amount="<?php echo $a * 1000; ?>">
                                ₦<?php echo number_format($a * 1000); ?>
                            </button>
                        <?php endforeach; ?>
                    </div>

                    <div class="relative mb-8">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 font-bold text-slate-400">₦</span>
                        <input type="number" id="custom-amount" placeholder="Other Amount" class="w-full bg-slate-50 border-2 border-slate-100 rounded-xl py-4 pl-10 pr-4 font-bold focus:border-emerald-600 outline-none transition-all" />
                    </div>

                    <button id="next-to-step-2" class="w-full bg-emerald-600 text-white py-4 rounded-xl font-bold text-lg shadow-lg shadow-emerald-200 hover:bg-emerald-700 transition-all flex items-center justify-center gap-2">
                        Continue
                    </button>
                </div>

                <!-- Step 2: Details -->
                <div id="step-2" class="hidden animate-in fade-in slide-in-from-bottom duration-500">
                    <h2 class="text-3xl font-bold text-slate-900 mb-6">Your Details</h2>
                        <!-- Identity Selection -->
                        <div class="mb-6">
                            <label class="block text-sm font-bold text-slate-700 mb-3">Donation Type</label>
                            <div class="grid grid-cols-2 gap-4">
                                <label class="cursor-pointer relative">
                                    <input type="radio" name="identity_radio" value="public" class="peer sr-only" checked onchange="toggleAnonymity(false)">
                                    <div class="p-4 rounded-xl border-2 border-slate-200 peer-checked:border-emerald-600 peer-checked:bg-emerald-50 transition-all text-center hover:border-emerald-300">
                                        <i data-lucide="globe" class="w-6 h-6 mx-auto mb-2 text-slate-500 peer-checked:text-emerald-600"></i>
                                        <div class="font-bold text-slate-900 peer-checked:text-emerald-800">Public</div>
                                        <div class="text-[10px] text-slate-500 mt-1">Show on donor lists</div>
                                    </div>
                                    <div class="absolute top-2 right-2 w-4 h-4 rounded-full border-2 border-slate-300 peer-checked:border-emerald-600 peer-checked:bg-emerald-600 flex items-center justify-center transition-all opacity-0 peer-checked:opacity-100">
                                        <i data-lucide="check" class="w-2.5 h-2.5 text-white"></i>
                                    </div>
                                </label>
                                <label class="cursor-pointer relative">
                                    <input type="radio" name="identity_radio" value="anonymous" class="peer sr-only" onchange="toggleAnonymity(true)">
                                    <div class="p-4 rounded-xl border-2 border-slate-200 peer-checked:border-emerald-600 peer-checked:bg-emerald-50 transition-all text-center hover:border-emerald-300">
                                        <i data-lucide="user-minus" class="w-6 h-6 mx-auto mb-2 text-slate-500 peer-checked:text-emerald-600"></i>
                                        <div class="font-bold text-slate-900 peer-checked:text-emerald-800">Anonymous</div>
                                        <div class="text-[10px] text-slate-500 mt-1">Hide all my details</div>
                                    </div>
                                    <div class="absolute top-2 right-2 w-4 h-4 rounded-full border-2 border-slate-300 peer-checked:border-emerald-600 peer-checked:bg-emerald-600 flex items-center justify-center transition-all opacity-0 peer-checked:opacity-100">
                                        <i data-lucide="check" class="w-2.5 h-2.5 text-white"></i>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <div id="personal-fields" class="space-y-6 transition-all duration-300 overflow-hidden">
                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-2">Full Name <span class="text-slate-400 font-normal ml-1">(Optional)</span></label>
                                <input type="text" id="donor-name" placeholder="E.g. Ahmed Musa" value="<?php echo $user ? htmlspecialchars($user['name']) : ''; ?>" class="w-full bg-slate-50 border-2 border-slate-100 rounded-xl py-3 px-4 font-medium outline-none focus:border-emerald-600" />
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-2">Email Address <span class="text-slate-400 font-normal ml-1">(Optional)</span></label>
                                <input type="email" id="donor-email" placeholder="Required only if you want a receipt" value="<?php echo $user ? htmlspecialchars($user['email']) : ''; ?>" class="w-full bg-slate-50 border-2 border-slate-100 rounded-xl py-3 px-4 font-medium outline-none focus:border-emerald-600" />
                            </div>
                        </div>

                    <div class="flex gap-4">
                        <button id="back-to-step-1" class="flex-1 py-4 font-bold text-slate-500 hover:text-slate-700 transition-colors">Back</button>
                        <button id="next-to-step-3" class="flex-[2] bg-emerald-600 text-white py-4 rounded-xl font-bold text-lg shadow-lg shadow-emerald-200 hover:bg-emerald-700 transition-all flex items-center justify-center gap-2">
                            Donate ₦<span class="final-amount">50,000</span>
                        </button>
                    </div>
                    
                    <div class="mt-6 flex items-center justify-center gap-2 text-slate-400 text-xs font-semibold">
                        <i data-lucide="shield-check" class="w-3.5 h-3.5"></i>
                        <span>Secured by Paystack</span>
                    </div>
                </div>

                <!-- Step 3: Payment Confirmation -->
                <div id="step-3" class="hidden animate-in zoom-in duration-500 text-center py-4">
                    <div id="payment-success-content" class="hidden">
                        <div class="w-20 h-20 bg-emerald-100 text-emerald-600 rounded-full flex items-center justify-center mx-auto mb-6">
                            <i data-lucide="check-circle" class="w-8 h-8"></i>
                        </div>
                        <h2 class="text-3xl font-bold text-slate-900 mb-2">Thank You!</h2>
                        <p class="text-slate-500 mb-8">Your donation has been successfully processed. A receipt has been sent to your email.</p>
                        
                        <div class="bg-slate-50 rounded-2xl p-6 text-left mb-8 border border-slate-100">
                            <div class="flex justify-between items-center mb-4">
                                <span class="text-slate-500 text-sm">Transaction Ref:</span>
                                <span id="ref-display" class="font-mono text-xs font-bold text-slate-700"></span>
                            </div>
                            <div id="ai-message-container" class="hidden">
                                <p class="text-emerald-900 italic font-medium" id="ai-message"></p>
                                <div class="mt-4 flex items-center gap-2 text-xs text-emerald-400 font-bold uppercase tracking-wider">
                                    <i data-lucide="heart" class="w-3.5 h-3.5 fill-emerald-400"></i> Heartfelt Thanks from Al-Shifah
                                </div>
                            </div>
                        </div>

                        <a href="index.php" class="inline-block bg-emerald-600 text-white px-8 py-3 rounded-xl font-bold hover:bg-emerald-700 transition-all">
                            Back to Home
                        </a>
                    </div>

                    <div id="payment-pending-content" class="py-12">
                        <div class="w-16 h-16 border-4 border-emerald-100 border-t-emerald-600 rounded-full animate-spin mx-auto mb-6"></div>
                        <h3 class="text-xl font-bold text-slate-900">Awaiting Confirmation</h3>
                        <p class="text-slate-500">Please complete the payment in the secure popup.</p>
                    </div>
                </div>
            </div>

            <!-- Sidebar Side -->
            <div class="md:col-span-2 bg-slate-900 p-8 md:p-12 text-white flex flex-col">
                <h3 class="text-xl font-bold mb-6 flex items-center gap-2 uppercase tracking-wide">
                    <i data-lucide="shield-check" class="text-emerald-400"></i> Al-Shifah Giving
                </h3>
                <div class="flex-grow space-y-8">
                    <p class="text-slate-400 text-sm leading-relaxed">
                        Your donation empowers AL-SHIFAH CHARITY FOUNDATION to drive real change in the community.
                    </p>
                    
                    <div class="space-y-4">
                        <div class="flex justify-between items-center py-3 border-b border-slate-800">
                            <span class="text-slate-400 text-sm font-medium">Type</span>
                            <span id="summary-type" class="font-bold">One-time</span>
                        </div>
                        <div class="flex justify-between items-center py-3 border-b border-slate-800">
                            <span class="text-slate-400 text-sm font-medium">Amount</span>
                            <span id="summary-amount" class="text-xl font-bold text-emerald-400">₦50,000</span>
                        </div>
                        <div class="flex justify-between items-center py-3 border-b border-slate-800">
                            <span class="text-slate-400 text-sm font-medium">Identity</span>
                            <span id="summary-identity" class="font-bold">Public</span>
                        </div>
                    </div>
                </div>

                <div class="mt-auto pt-8">
                    <p class="text-xs text-slate-500 leading-relaxed italic">
                        Securely handled by Paystack. AL-SHIFAH CHARITY FOUNDATION ensures your data is protected.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    let currentStep = 1;
    let amount = 50000;
    let isRecurring = false;
    let isAnonymous = false;

    const sections = {
        1: document.getElementById('step-1'),
        2: document.getElementById('step-2'),
        3: document.getElementById('step-3')
    };

    const indicators = document.querySelectorAll('.step-indicator');

    function updateSummary() {
        document.getElementById('summary-type').textContent = isRecurring ? 'Monthly' : 'One-time';
        document.getElementById('summary-amount').textContent = '₦' + amount.toLocaleString();
        document.getElementById('summary-identity').textContent = isAnonymous ? 'Anonymous' : (document.getElementById('donor-name').value || 'Public');
        document.querySelectorAll('.final-amount').forEach(el => el.textContent = '₦' + amount.toLocaleString());
    }

    function goToStep(step) {
        sections[currentStep].classList.add('hidden');
        sections[step].classList.remove('hidden');
        
        indicators.forEach((ind, idx) => {
            const circle = ind.querySelector('.step-circle');
            const line = ind.querySelector('.step-line');
            const s = idx + 1;
            
            if (s < step) {
                circle.classList.replace('bg-slate-200', 'bg-emerald-600');
                circle.classList.replace('text-slate-500', 'text-white');
                circle.innerHTML = '<i data-lucide="check-circle" class="w-4 h-4"></i>';
                if (line) line.classList.replace('bg-slate-200', 'bg-emerald-600');
            } else if (s === step) {
                circle.classList.replace('bg-slate-200', 'bg-emerald-600');
                circle.classList.replace('text-slate-500', 'text-white');
                circle.textContent = s;
                if (line) line.classList.replace('bg-emerald-600', 'bg-slate-200');
            } else {
                circle.classList.replace('bg-emerald-600', 'bg-slate-200');
                circle.classList.replace('text-white', 'text-slate-500');
                circle.textContent = s;
                if (line) line.classList.replace('bg-emerald-600', 'bg-slate-200');
            }
        });
        
        lucide.createIcons();
        currentStep = step;
        updateSummary();
    }

    // Step 1 Listeners
    document.getElementById('btn-one-time').addEventListener('click', () => {
        isRecurring = false;
        document.getElementById('btn-one-time').classList.add('bg-white', 'shadow', 'text-emerald-600');
        document.getElementById('btn-one-time').classList.remove('text-slate-500');
        document.getElementById('btn-monthly').classList.remove('bg-white', 'shadow', 'text-emerald-600');
        document.getElementById('btn-monthly').classList.add('text-slate-500');
        updateSummary();
    });

    document.getElementById('btn-monthly').addEventListener('click', () => {
        isRecurring = true;
        document.getElementById('btn-monthly').classList.add('bg-white', 'shadow', 'text-emerald-600');
        document.getElementById('btn-monthly').classList.remove('text-slate-500');
        document.getElementById('btn-one-time').classList.remove('bg-white', 'shadow', 'text-emerald-600');
        document.getElementById('btn-one-time').classList.add('text-slate-500');
        updateSummary();
    });

    document.querySelectorAll('.amount-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            document.querySelectorAll('.amount-btn').forEach(b => b.classList.remove('border-emerald-600', 'bg-emerald-50', 'text-emerald-600'));
            btn.classList.add('border-emerald-600', 'bg-emerald-50', 'text-emerald-600');
            amount = parseInt(btn.dataset.amount);
            document.getElementById('custom-amount').value = '';
            updateSummary();
        });
    });

    document.getElementById('custom-amount').addEventListener('input', (e) => {
        document.querySelectorAll('.amount-btn').forEach(b => b.classList.remove('border-emerald-600', 'bg-emerald-50', 'text-emerald-600'));
        amount = parseInt(e.target.value) || 0;
        updateSummary();
    });

    document.getElementById('next-to-step-2').addEventListener('click', () => goToStep(2));

    // Step 2 Listeners
    document.getElementById('back-to-step-1').addEventListener('click', () => goToStep(1));
    function toggleAnonymity(val) {
        isAnonymous = val;
        const fields = document.getElementById('personal-fields');
        if (isAnonymous) {
            fields.style.maxHeight = '0px';
            fields.style.opacity = '0';
            fields.style.pointerEvents = 'none';
        } else {
            fields.style.maxHeight = '500px';
            fields.style.opacity = '1';
            fields.style.pointerEvents = 'auto';
        }
        updateSummary();
    }
    
    // Set initial display
    document.getElementById('personal-fields').style.maxHeight = '500px';
    document.getElementById('personal-fields').style.opacity = '1';

    document.getElementById('donor-name').addEventListener('input', updateSummary);

    function payWithPaystack() {
        // If email is empty but they want to donate publicly, we still just use fallback so it works without stopping them
        let rawEmail = document.getElementById('donor-email').value.trim();
        const donorEmail = rawEmail !== '' ? rawEmail : 'fallback@alshifah.org';
        const donorName = isAnonymous ? 'Anonymous' : (document.getElementById('donor-name').value || 'Generous Donor');
        
        try {
            const handler = PaystackPop.setup({
            key: '<?php echo $OFFICIAL_INFO['paystackPublicKey']; ?>',
            email: isAnonymous ? 'anonymous@alshifah.org' : donorEmail,
            amount: amount * 100, // Amount in Kobo
            currency: 'NGN', 
            ref: 'ALSF-' + Math.floor((Math.random() * 1000000000) + 1),
            metadata: {
                custom_fields: [
                    {
                        display_name: "Donor Name",
                        variable_name: "donor_name",
                        value: donorName
                    },
                    {
                        display_name: "Is Anonymous",
                        variable_name: "is_anonymous",
                        value: isAnonymous ? "Yes" : "No"
                    }
                ]
            },
            callback: function(response) {
                // Payment successful
                finalizeDonation(response.reference);
            },
            onClose: function() {
                alert('Payment window closed.');
                const btn = document.getElementById('next-to-step-3');
                btn.disabled = false;
                btn.textContent = 'Donate ₦' + amount.toLocaleString();
                lucide.createIcons();
                goToStep(2);
            }
        });
        handler.openIframe();
        } catch (error) {
            console.error("Paystack Error:", error);
            alert("Could not initialize payment: " + error.message);
            goToStep(2);
            const btn = document.getElementById('next-to-step-3');
            btn.disabled = false;
            btn.textContent = 'Donate ₦' + amount.toLocaleString();
            lucide.createIcons();
        }
    }

    async function finalizeDonation(reference) {
        const donorName = isAnonymous ? 'Anonymous' : (document.getElementById('donor-name').value || 'Generous Donor');
        const campaignId = <?php echo json_encode($id); ?>;

        document.getElementById('payment-pending-content').classList.add('hidden');
        document.getElementById('payment-success-content').classList.remove('hidden');
        document.getElementById('ref-display').textContent = reference;

        try {
            const response = await fetch('process_donation.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    amount: amount,
                    donor_name: donorName,
                    is_anonymous: isAnonymous,
                    campaign_id: campaignId,
                    payment_reference: reference
                })
            });

            const result = await response.json();

            if (result.success) {
                const msg = `Dear ${donorName}, your contribution of ₦${amount.toLocaleString()} to Al-Shifah is a beacon of hope. Reference: ${reference}. May your kindness be rewarded multiple folds.`;
                document.getElementById('ai-message').textContent = msg;
                document.getElementById('ai-message-container').classList.remove('hidden');
                lucide.createIcons();
            }
        } catch (error) {
            console.error('Error recording donation:', error);
        }
    }

    document.getElementById('next-to-step-3').addEventListener('click', () => {
        // Everything is truly optional now, just move forward
        const btn = document.getElementById('next-to-step-3');
        btn.disabled = true;
        btn.innerHTML = '<i data-lucide="loader-2" class="w-5 h-5 animate-spin"></i> Initializing...';
        lucide.createIcons();
        
        goToStep(3);
        payWithPaystack();
    });
</script>

<?php include_once 'includes/footer.php'; ?>
