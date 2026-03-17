<?php
// includes/admin_cms.php — Website Builder Tabs

if ($tab === 'cms_homepage'):
    $hpSections = $pdo->query("SELECT * FROM homepage_sections ORDER BY order_rank ASC")->fetchAll();
    // Pre-fetch blocks for homepage to make it easy if we were editing inline, but we'll stick to just order/visibility for this tab to keep it clean.
?>
<div class="section-header"><p class="section-title">Homepage Layout</p><p class="section-sub">Control the order and visibility of homepage sections.</p></div>
<div class="card">
  <form method="POST">
    <input type="hidden" name="sections_action" value="update">
    <table class="data-table">
      <thead><tr><th>Section</th><th>Description</th><th>Visible</th><th>Order</th></tr></thead>
      <tbody>
        <?php foreach ($hpSections as $sec): ?>
        <tr>
          <td><strong><?php echo htmlspecialchars($sec['label']); ?></strong></td>
          <td style="font-size:12px;color:var(--muted);"><?php echo htmlspecialchars($sec['description']); ?></td>
          <td>
            <label class="toggle">
              <input type="checkbox" name="sections[<?php echo $sec['section_key']; ?>][visible]" <?php echo $sec['is_visible']?'checked':''; ?>>
              <span class="toggle-slider"></span>
            </label>
          </td>
          <td style="width:80px;">
            <input type="number" name="sections[<?php echo $sec['section_key']; ?>][order]" value="<?php echo $sec['order_rank']; ?>" class="form-input" style="padding:4px 8px;text-align:center;">
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
    <div style="padding:12px 20px;border-top:1px solid var(--border);">
      <button class="btn btn-primary">Save Layout</button>
    </div>
  </form>
</div>

<?php elseif ($tab === 'cms_hero'):
    $pageName = $_GET['page'] ?? 'home';
    $stmt = $pdo->prepare("SELECT * FROM page_content WHERE page_name=? AND section_name='hero'");
    $stmt->execute([$pageName]);
    $hero = $stmt->fetch() ?: [];

    $slides = $pdo->prepare("SELECT * FROM hero_slides WHERE page_name=? ORDER BY order_rank ASC");
    $slides->execute([$pageName]);
    $slideList = $slides->fetchAll();
?>
<div class="section-header">
  <div><p class="section-title">Hero Section</p><p class="section-sub">Manage the top banner for your pages.</p></div>
  <div style="display:flex;gap:8px;background:#fff;padding:4px;border-radius:8px;border:1px solid var(--border);">
    <?php foreach(['home'=>'Home','about'=>'About','contact'=>'Contact'] as $k=>$v): ?>
    <a href="?tab=cms_hero&page=<?php echo $k; ?>" class="btn btn-sm <?php echo $pageName===$k?'btn-primary':'btn-secondary'; ?>" style="font-size:11px;"><?php echo $v; ?></a>
    <?php endforeach; ?>
  </div>
</div>

<div style="display:grid;grid-template-columns:1fr 340px;gap:16px;align-items:start;">
  <div class="card">
    <div class="card-header"><h2><?php echo ucfirst($pageName); ?> Hero Settings</h2></div>
    <div class="card-body">
      <form method="POST">
        <input type="hidden" name="hero_action" value="update">
        <input type="hidden" name="page_name" value="<?php echo $pageName; ?>">
        
        <div class="form-group">
          <label class="form-label">Hero Mode</label>
          <select name="hero_type" class="form-input form-select" onchange="toggleHeroMode(this, '<?php echo $pageName; ?>')">
            <option value="image" <?php echo ($hero['hero_type']??'')==='image'?'selected':''; ?>>Single Image</option>
            <option value="slideshow" <?php echo ($hero['hero_type']??'')==='slideshow'?'selected':''; ?>>Slideshow</option>
            <option value="video" <?php echo ($hero['hero_type']??'')==='video'?'selected':''; ?>>Video Background</option>
          </select>
        </div>

        <div class="form-group" id="vf_<?php echo $pageName; ?>" style="display:<?php echo ($hero['hero_type']??'')==='video'?'block':'none'; ?>;background:#f9fafb;padding:12px;border-radius:8px;border:1px solid var(--border);">
          <label class="form-label text-blue-600">Video File</label>
          <?php uploadWidget("vid-upload-{$pageName}",'video_url',$hero['video_url']??'','video/*','Video'); ?>
        </div>

        <div class="form-group">
          <label class="form-label">Main Image (Fallback/Single)</label>
          <?php uploadWidget("img-upload-{$pageName}",'image_url',$hero['image_url']??'','image/*','Image'); ?>
        </div>

        <div class="form-grid">
          <div class="form-group"><label class="form-label">Badge Text</label><input type="text" name="badge_text" value="<?php echo htmlspecialchars($hero['badge_text']??''); ?>" class="form-input"></div>
          <div class="form-group"><label class="form-label">Overlay Opacity (0.0 - 1.0)</label><input type="text" name="overlay_opacity" value="<?php echo htmlspecialchars($hero['overlay_opacity']??'0.6'); ?>" class="form-input"></div>
        </div>
        
        <div class="form-group"><label class="form-label">Main Title</label><input type="text" name="title" value="<?php echo htmlspecialchars($hero['title']??''); ?>" class="form-input"></div>
        <div class="form-group"><label class="form-label">Subtitle</label><textarea name="subtitle" class="form-input" rows="2"><?php echo htmlspecialchars($hero['subtitle']??''); ?></textarea></div>
        
        <div class="form-grid">
          <div class="form-group"><label class="form-label">Primary Button Text</label><input type="text" name="btn_primary_text" value="<?php echo htmlspecialchars($hero['btn_primary_text']??'Donate Now'); ?>" class="form-input"></div>
          <div class="form-group"><label class="form-label">Primary Button URL</label><input type="text" name="btn_primary_url" value="<?php echo htmlspecialchars($hero['btn_primary_url']??'donate.php'); ?>" class="form-input"></div>
          <div class="form-group"><label class="form-label">Secondary Btn Text</label><input type="text" name="btn_secondary_text" value="<?php echo htmlspecialchars($hero['btn_secondary_text']??''); ?>" class="form-input"></div>
          <div class="form-group"><label class="form-label">Secondary Btn URL</label><input type="text" name="btn_secondary_url" value="<?php echo htmlspecialchars($hero['btn_secondary_url']??''); ?>" class="form-input"></div>
        </div>

        <button class="btn btn-primary">Save Hero Settings</button>
      </form>
    </div>
  </div>

  <?php if (($hero['hero_type']??'') === 'slideshow'): ?>
  <div class="card">
    <div class="card-header"><h2>Slideshow Images</h2></div>
    <div class="card-body">
      <div style="display:grid;gap:10px;margin-bottom:16px;">
        <?php foreach ($slideList as $sl): ?>
        <div style="display:flex;align-items:center;gap:10px;padding:6px;border:1px solid var(--border);border-radius:8px;background:#f9fafb;">
          <img src="<?php echo htmlspecialchars($sl['image_url']); ?>" style="width:40px;height:40px;border-radius:4px;object-fit:cover;">
          <div style="flex:1;min-width:0;">
            <div style="font-size:11px;color:var(--muted);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;"><?php echo htmlspecialchars($sl['caption']??'No caption'); ?></div>
          </div>
          <form method="POST" onsubmit="return confirm('Remove slide?');">
            <input type="hidden" name="slide_action" value="delete">
            <input type="hidden" name="slide_id" value="<?php echo $sl['id']; ?>">
            <button class="btn btn-sm btn-danger btn-icon"><i data-lucide="x" style="width:12px;height:12px;"></i></button>
          </form>
        </div>
        <?php endforeach; ?>
      </div>
      
      <form method="POST" style="border-top:1px solid var(--border);padding-top:12px;">
        <input type="hidden" name="slide_action" value="add">
        <label class="form-label">Add Slide</label>
        <?php uploadWidget('slide-upload','slide_url','','image/*','Slide Image'); ?>
        <input type="text" name="slide_caption" class="form-input" placeholder="Caption" style="margin-top:8px;">
        <button class="btn btn-secondary w-full" style="width:100%;margin-top:8px;">Add Slide</button>
      </form>
    </div>
  </div>
  <?php endif; ?>
</div>

<?php elseif ($tab === 'cms_about'):
    $blocks = $pdo->query("SELECT block_key, block_value FROM content_blocks WHERE section_key='about'")->fetchAll(PDO::FETCH_KEY_PAIR);
    $team = $pdo->query("SELECT * FROM team_members ORDER BY order_rank ASC")->fetchAll();
    
    // Fallbacks
    $B = fn($k, $d='') => htmlspecialchars($blocks[$k] ?? $d);
?>
<div class="section-header"><p class="section-title">About Page Config</p><p class="section-sub">Manage text and team members on the About page.</p></div>
<div style="display:grid;grid-template-columns:1fr 380px;gap:16px;align-items:start;">
  <div class="card">
    <div class="card-header"><h2>Team Members</h2></div>
    <table class="data-table">
      <thead><tr><th>Name</th><th>Role & Type</th><th></th></tr></thead>
      <tbody>
        <?php foreach ($team as $tm): ?>
        <tr>
          <td>
            <?php if(!empty($tm['photo_url'])): ?><img src="<?php echo htmlspecialchars($tm['photo_url']); ?>" style="width:32px;height:32px;border-radius:16px;object-fit:cover;margin-right:8px;vertical-align:middle;"><?php endif; ?>
            <strong><?php echo htmlspecialchars($tm['name']); ?></strong>
          </td>
          <td>
            <div style="font-size:12px;"><?php echo htmlspecialchars($tm['role']); ?></div>
            <span class="badge-pill badge-blue" style="font-size:9px;"><?php echo htmlspecialchars($tm['type']); ?></span>
          </td>
          <td style="display:flex;gap:6px;">
            <button type="button" class="btn btn-sm btn-secondary btn-icon" onclick="editTeam(<?php echo $tm['id']; ?>,'<?php echo htmlspecialchars(addslashes($tm['name'])); ?>','<?php echo htmlspecialchars(addslashes($tm['role'])); ?>','<?php echo htmlspecialchars(addslashes($tm['bio']??'')); ?>','<?php echo htmlspecialchars(addslashes($tm['photo_url']??'')); ?>','<?php echo htmlspecialchars(addslashes($tm['type']??'Trustee')); ?>')"><i data-lucide="pencil" style="width:13px;height:13px;"></i></button>
            <form method="POST" onsubmit="return confirm('Remove member?');"><input type="hidden" name="team_action" value="delete"><input type="hidden" name="team_id" value="<?php echo $tm['id']; ?>"><button class="btn btn-sm btn-danger btn-icon"><i data-lucide="trash-2" style="width:13px;height:13px;"></i></button></form>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  
  <div class="flex-col" style="display:flex;flex-direction:column;gap:16px;">
    <!-- Team Form -->
    <div class="card" id="team-form">
      <div class="card-header"><h2 id="team-form-title">Add Member</h2><button type="button" onclick="resetTeamForm()" class="btn btn-sm btn-secondary font-xs" style="font-size:10px;">Reset</button></div>
      <div class="card-body">
        <form method="POST">
          <input type="hidden" name="team_action" value="save">
          <input type="hidden" name="team_id" id="team_id">
          <div class="form-group"><label class="form-label">Name</label><input type="text" name="name" id="tm_name" class="form-input" required></div>
          <div class="form-grid">
            <div class="form-group"><label class="form-label">Role</label><input type="text" name="role" id="tm_role" class="form-input" required></div>
            <div class="form-group"><label class="form-label">Type</label>
              <select name="type" id="tm_type" class="form-input form-select">
                <option value="Trustee">Trustee</option>
                <option value="Founder">Founder</option>
                <option value="Staff">Staff</option>
              </select>
            </div>
          </div>
          <div class="form-group"><label class="form-label">Bio</label><textarea name="bio" id="tm_bio" class="form-input" rows="2"></textarea></div>
          <div class="form-group"><label class="form-label">Photo</label>
            <?php uploadWidget('team-photo-up','photo_url','','image/*','Photo'); ?>
            <input type="hidden" id="team-photo-field" name="photo_url"> <!-- Will be synced by the widget, but we override id to map it correctly if needed. Actually let's use the fieldId properly. -->
          </div>
          <button class="btn btn-primary" style="width:100%;">Save Member</button>
        </form>
      </div>
    </div>
    
    <!-- Text Blocks -->
    <div class="card">
      <div class="card-header"><h2>Text Content</h2></div>
      <div class="card-body">
        <form method="POST">
          <input type="hidden" name="about_blocks_action" value="1">
          <div class="form-group"><label class="form-label">Intro Badge</label><input type="text" name="about_blocks[intro_badge]" value="<?php echo $B('intro_badge'); ?>" class="form-input"></div>
          <div class="form-group"><label class="form-label">Intro Heading</label><input type="text" name="about_blocks[intro_heading]" value="<?php echo $B('intro_heading'); ?>" class="form-input"></div>
          <div class="form-group"><label class="form-label">Intro Quote</label><textarea name="about_blocks[intro_quote]" class="form-input" rows="2"><?php echo $B('intro_quote'); ?></textarea></div>
          <div class="form-group"><label class="form-label">HQ Label</label><input type="text" name="about_blocks[hq_label]" value="<?php echo $B('hq_label'); ?>" class="form-input"></div>
          <div class="form-group"><label class="form-label">Governance Head</label><input type="text" name="about_blocks[governance_heading]" value="<?php echo $B('governance_heading'); ?>" class="form-input"></div>
          <button class="btn btn-primary" style="width:100%;">Save Text</button>
        </form>
      </div>
    </div>
  </div>
</div>
<!-- Override JS for team edit to sync with upload widget -->
<script>
function editTeam(id,name,role,bio,photo,type) {
  document.getElementById('team_id').value=id;
  document.getElementById('tm_name').value=name;
  document.getElementById('tm_role').value=role;
  document.getElementById('tm_bio').value=bio;
  const sel=document.getElementById('tm_type');
  if(sel) [...sel.options].forEach(o=>o.selected=o.value===type);
  document.getElementById('team-form-title').textContent='Edit Member';
  
  // Set the hidden field and show preview in widget
  const f=document.getElementById('photo_url'); if(f) f.value=photo;
  const p=document.querySelector('#team-photo-up .preview-area'); if(p) p.style.display = photo?'flex':'none';
  const i=document.querySelector('#team-photo-up .preview-thumb'); if(i) { i.src = photo; i.style.display = photo?'block':'none'; }
  const ic=document.querySelector('#team-photo-up .preview-icon'); if(ic) ic.style.display = photo?'none':'block';

  document.getElementById('team-form').scrollIntoView({behavior:'smooth',block:'center'});
}
function resetTeamForm() {
  ['team_id','tm_name','tm_role','tm_bio','photo_url'].forEach(id=>{const el=document.getElementById(id);if(el)el.value='';});
  document.getElementById('team-form-title').textContent='Add Member';
  const p=document.querySelector('#team-photo-up .preview-area'); if(p) p.style.display='none';
  const i=document.querySelector('#team-photo-up .preview-thumb'); if(i) { i.src = ''; i.style.display='none'; }
  const ic=document.querySelector('#team-photo-up .preview-icon'); if(ic) ic.style.display='block';
}
</script>

<?php elseif ($tab === 'cms_contact'):
    $settings = $pdo->query("SELECT setting_key, setting_value FROM contact_settings")->fetchAll(PDO::FETCH_KEY_PAIR);
    $blocks = $pdo->query("SELECT block_key, block_value FROM content_blocks WHERE section_key='contact'")->fetchAll(PDO::FETCH_KEY_PAIR);
    $S = fn($k) => htmlspecialchars($settings[$k] ?? '');
    $B = fn($k) => htmlspecialchars($blocks[$k] ?? '');
?>
<div class="section-header"><p class="section-title">Contact Page</p><p class="section-sub">Manage contact details, map, and form labels.</p></div>
<form method="POST">
  <input type="hidden" name="contact_settings_action" value="1">
  <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
    <div class="card">
      <div class="card-header"><h2>Contact Info</h2></div>
      <div class="card-body" style="display:flex;flex-direction:column;gap:12px;">
        <div class="form-group"><label class="form-label">Primary Email</label><input type="email" name="contact[email]" value="<?php echo $S('email'); ?>" class="form-input"></div>
        <div class="form-grid">
          <div class="form-group"><label class="form-label">Phone 1</label><input type="text" name="contact[phone_1]" value="<?php echo $S('phone_1'); ?>" class="form-input"></div>
          <div class="form-group"><label class="form-label">Phone 2 (Optional)</label><input type="text" name="contact[phone_2]" value="<?php echo $S('phone_2'); ?>" class="form-input"></div>
        </div>
        <div class="form-group"><label class="form-label">Full Address</label><textarea name="contact[address]" class="form-input" rows="2"><?php echo $S('address'); ?></textarea></div>
        <div class="form-group"><label class="form-label">Office Hours</label><input type="text" name="contact[office_hours]" value="<?php echo $S('office_hours'); ?>" class="form-input"></div>
        <div class="form-group"><label class="form-label">Google Maps Embed URL</label><input type="text" name="contact[map_embed]" value="<?php echo $S('map_embed'); ?>" class="form-input" placeholder="https://maps.google.com/maps?..."></div>
      </div>
    </div>
    <div class="card">
      <div class="card-header"><h2>Page Text</h2></div>
      <div class="card-body" style="display:flex;flex-direction:column;gap:12px;">
        <div class="form-group"><label class="form-label">Heading</label><input type="text" name="contact_blocks[heading]" value="<?php echo $B('heading'); ?>" class="form-input"></div>
        <div class="form-group"><label class="form-label">Subheading</label><textarea name="contact_blocks[subheading]" class="form-input" rows="2"><?php echo $B('subheading'); ?></textarea></div>
        <div class="form-group"><label class="form-label">Form Heading</label><input type="text" name="contact_blocks[form_heading]" value="<?php echo $B('form_heading'); ?>" class="form-input"></div>
      </div>
    </div>
  </div>
  <div style="margin-top:16px;"><button class="btn btn-primary">Save Contact Settings</button></div>
</form>

<?php elseif ($tab === 'cms_nav'):
    $blocks = $pdo->query("SELECT block_key, block_value FROM content_blocks WHERE section_key='footer'")->fetchAll(PDO::FETCH_KEY_PAIR);
?>
<div class="section-header"><p class="section-title">Navigation & Footer</p><p class="section-sub">Settings for the global header and footer area.</p></div>
<div class="card" style="max-width:600px;">
  <form method="POST">
    <input type="hidden" name="nav_settings_action" value="1">
    <div class="card-body" style="display:flex;flex-direction:column;gap:14px;">
      <div class="form-group"><label class="form-label">Site Name (Navbar)</label><input type="text" name="nav_settings[site_name]" value="<?php echo htmlspecialchars(getSetting('site_name', $pdo)); ?>" class="form-input"></div>
      <div class="form-group"><label class="form-label">Footer Tagline</label><textarea name="footer_blocks[tagline]" class="form-input" rows="2"><?php echo htmlspecialchars($blocks['tagline']??''); ?></textarea></div>
      
      <p style="font-size:12px;color:var(--muted);font-weight:600;margin-top:10px;text-transform:uppercase;">Social Links</p>
      <div class="form-group"><label class="form-label">Facebook</label><input type="text" name="nav_settings[facebook_url]" value="<?php echo htmlspecialchars(getSetting('facebook_url', $pdo)); ?>" class="form-input"></div>
      <div class="form-group"><label class="form-label">Twitter / X</label><input type="text" name="nav_settings[twitter_url]" value="<?php echo htmlspecialchars(getSetting('twitter_url', $pdo)); ?>" class="form-input"></div>
      <div class="form-group"><label class="form-label">Instagram</label><input type="text" name="nav_settings[instagram_url]" value="<?php echo htmlspecialchars(getSetting('instagram_url', $pdo)); ?>" class="form-input"></div>
    </div>
    <div style="padding:12px 20px;border-top:1px solid var(--border);">
      <button class="btn btn-primary">Save Nav & Footer</button>
    </div>
  </form>
</div>
<?php endif; ?>
