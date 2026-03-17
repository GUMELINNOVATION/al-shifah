-- Al-Shifah Charity Foundation Database Schema

CREATE DATABASE IF NOT EXISTS al_shifah_db;
USE al_shifah_db;

-- 1. Admins Table
CREATE TABLE IF NOT EXISTS admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 1b. Users Table (Donors)
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(20),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert default admin (Password is 'admin123' - user should change it)
INSERT INTO admins (username, password, email) VALUES 
('admin', '$2y$12$.tJKmFVxXwr1Tf0hgNtsoeFCY3k6mIVKqx29OHaeun9/TWrQwZc9i', 'admin@alshifah.org');

-- 2. Site Settings Table (Global)
CREATE TABLE IF NOT EXISTS site_settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(50) NOT NULL UNIQUE,
    setting_value TEXT,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Initial Settings
INSERT INTO site_settings (setting_key, setting_value) VALUES 
('site_name', 'Al-Shifah Charity Foundation'),
('contact_email', 'al.shifahcharityfoundation@gmail.com'),
('contact_phone_1', '+234 913 856 3760'),
('contact_phone_2', '+234 706 536 4835'),
('address', 'No. 07, Khadi Bulama Street, Commissioners Quarters, Damaturu, Yobe State, Nigeria'),
('chairman', 'Muhammad Abubakar Abdullahi'),
('secretary', 'Gumel Mustapha Ali'),
('registration_date', 'January 30, 2025'),
('facebook_url', '#'),
('twitter_url', '#'),
('instagram_url', '#');

-- 3. Page Content Table (Hero & Section Control)
CREATE TABLE IF NOT EXISTS page_content (
    id INT AUTO_INCREMENT PRIMARY KEY,
    page_name VARCHAR(50) NOT NULL,
    section_name VARCHAR(50) NOT NULL,
    title VARCHAR(500),
    subtitle TEXT,
    content TEXT,
    image_url VARCHAR(500),
    -- Hero CMS Fields
    hero_type ENUM('image','slideshow','video') DEFAULT 'image',
    video_url VARCHAR(500),
    badge_text VARCHAR(255) DEFAULT 'Official Al-Shifah Charity Foundation',
    overlay_opacity VARCHAR(10) DEFAULT '0.6',
    btn_primary_text VARCHAR(100) DEFAULT 'Donate Now',
    btn_primary_url VARCHAR(255) DEFAULT 'donate.php',
    btn_primary_color VARCHAR(20) DEFAULT 'emerald',
    btn_secondary_text VARCHAR(100) DEFAULT 'Our Campaigns',
    btn_secondary_url VARCHAR(255) DEFAULT 'campaigns.php',
    UNIQUE KEY (page_name, section_name)
);

-- Initial Page Content
INSERT INTO page_content (page_name, section_name, title, subtitle, image_url, hero_type, badge_text, btn_primary_text, btn_primary_url, btn_secondary_text, btn_secondary_url) VALUES 
('home', 'hero', 'Empowering Communities, Changing Lives.', 'A non-profit dedicated to establishing essential services and providing humanitarian support through the provision of basic needs.', 'https://images.unsplash.com/photo-1488521787991-ed7bbaae773c?auto=format&fit=crop&q=80&w=1600', 'image', 'Official Al-Shifah Charity Foundation', 'Donate Now', 'donate.php', 'Our Campaigns', 'campaigns.php'),
('about', 'hero', 'A Not-for-Profit & Non-Political Organisation.', 'We, the members of AL-SHIFAH CHARITY FOUNDATION... firmly and solemnly resolve to provide for ourselves a constitution and to be governed by the provisions therein contained.', 'https://images.unsplash.com/photo-1488521787991-ed7bbaae773c?auto=format&fit=crop&q=80&w=600', 'image', 'About Al-Shifah', 'View Our Mission', 'campaigns.php', 'Contact Us', 'contact.php'),
('contact', 'hero', 'Get In Touch With Us', 'Reach out with your questions, partnership proposals, or to learn more about our humanitarian programs.', 'https://images.unsplash.com/photo-1588681664899-f142ff2dc9b1?auto=format&fit=crop&q=80&w=1600', 'image', 'Contact Al-Shifah', 'Join Us', 'login.php', 'View Campaigns', 'campaigns.php');

-- 3b. Hero Slideshow Slides
CREATE TABLE IF NOT EXISTS hero_slides (
    id INT AUTO_INCREMENT PRIMARY KEY,
    page_name VARCHAR(50) NOT NULL DEFAULT 'home',
    image_url VARCHAR(500) NOT NULL,
    caption VARCHAR(500),
    link_url VARCHAR(255),
    order_rank INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Sample slides
INSERT INTO hero_slides (page_name, image_url, caption, order_rank) VALUES
('home', 'https://images.unsplash.com/photo-1488521787991-ed7bbaae773c?auto=format&fit=crop&q=80&w=1600', 'Changing lives in Northern Nigeria.', 1),
('home', 'https://images.unsplash.com/photo-1509062522246-3755977927d7?auto=format&fit=crop&q=80&w=1600', 'Education programs for orphans and youth.', 2),
('home', 'https://images.unsplash.com/photo-1542601906990-b4d3fb778b09?auto=format&fit=crop&q=80&w=1600', 'WASH & health infrastructure for communities.', 3);

-- 3c. Homepage Section Order & Visibility
CREATE TABLE IF NOT EXISTS homepage_sections (
    id INT AUTO_INCREMENT PRIMARY KEY,
    section_key VARCHAR(50) UNIQUE NOT NULL,
    label VARCHAR(100) NOT NULL,
    description VARCHAR(255),
    is_visible BOOLEAN DEFAULT TRUE,
    order_rank INT DEFAULT 0
);

INSERT INTO homepage_sections (section_key, label, description, is_visible, order_rank) VALUES
('hero',      'Hero / Banner',        'The top hero area of the homepage',             1, 1),
('tracker',   'Impact Tracker',       'Fundraising progress bar & totals',             1, 2),
('stats',     'Impact Statistics',    'Key numbers: kids, families, meals reached',    1, 3),
('mission',   'Mission Summary',      'Why We Exist — the two-column text + image',   1, 4),
('campaigns', 'Active Campaigns',     'Featured campaign cards',                       1, 5),
('gallery',   'Photo Gallery',        'Visual gallery grid from field activities',     1, 6),
('blog',      'Latest Blog Posts',    'Latest news & insights cards',                  1, 7);

-- 3d. Reusable Content Blocks (editable text for any section)
CREATE TABLE IF NOT EXISTS content_blocks (
    id INT AUTO_INCREMENT PRIMARY KEY,
    section_key VARCHAR(50) NOT NULL,
    block_key VARCHAR(100) NOT NULL,
    block_value TEXT,
    UNIQUE KEY (section_key, block_key)
);

INSERT INTO content_blocks (section_key, block_key, block_value) VALUES
('tracker',   'heading',           'Collective Progress'),
('tracker',   'subheading',        'Tracking every Naira donated toward our community essential goals.'),
('tracker',   'badge_label',       'Live Impact Tracker'),
('stats',     'visible',           '1'),
('mission',   'badge',             'Why We Exist'),
('mission',   'heading',           'Establishing services within the community to make a difference.'),
('mission',   'paragraph',         'Our mission is to render charitable and humanitarian services geared toward supporting the needy and less privileged members of the community through the provision of basic needs.'),
('mission',   'cta_text',          'Learn about our Constitution'),
('mission',   'cta_url',           'about.php'),
('mission',   'image_url',         'https://images.unsplash.com/photo-1542601906990-b4d3fb778b09?auto=format&fit=crop&q=80&w=800'),
('mission',   'quote',             '\"We firmly resolve to provide for ourselves a constitution and to be governed by service.\"'),
('campaigns', 'heading',           'Active Support Programs'),
('campaigns', 'subheading',        'Targeted initiatives providing immediate and long-term relief.'),
('campaigns', 'cta_text',          'View All'),
('gallery',   'badge',             'Our Visual Impact'),
('gallery',   'heading',           'Field Update Gallery'),
('gallery',   'subheading',        'A window into our work across Nigeria, establishing services and providing relief materials.'),
('blog',      'heading',           'Latest Insights'),
('blog',      'subheading',        'In-depth stories about our programs and constitutional achievements.'),
('blog',      'cta_text',          'Read All Updates'),
('about',     'intro_badge',       'Official Constitution: Article 1'),
('about',     'intro_heading',     'A Not-for-Profit & Non-Political Organisation.'),
('about',     'intro_quote',       '\"We, the members of AL-SHIFAH CHARITY FOUNDATION... firmly and solemnly resolve to provide for ourselves a constitution and to be governed by the provisions therein contained.\"'),
('about',     'intro_paragraph',   'Based in Damaturu, Nigeria, Al-Shifah Charity Foundation is committed to humanitarian excellence, governed by a strict code of ethics that ensures all resources are applied solely to the promotion of community objectives.'),
('about',     'hq_label',          'Headquarters'),
('about',     'governance_heading','Governing Body'),
('about',     'governance_body',   'As per Article 7, the general administration is governed by the Incorporated Trustees which consist of the Chairman, Secretary, and other dedicated members.'),
('about',     'bullet_1',          'Audited annually by independent licenced Auditors.'),
('about',     'bullet_2',          '2/3 majority votes required for key trustee decisions.'),
('about',     'bullet_3',          'Zero profit distribution to members (Article 13).'),
('contact',   'heading',           'Get In Touch'),
('contact',   'subheading',        'Reach out with your questions, partnership proposals, or to learn more about our humanitarian programs.'),
('contact',   'form_heading',      'Send Us a Message'),
('contact',   'map_embed_url',     'https://maps.google.com/maps?q=Damaturu,+Yobe+State,+Nigeria&output=embed'),
('footer',    'tagline',           'Committed to humanitarian excellence and community service.'),
('footer',    'col2_heading',      'Quick Links'),
('footer',    'col3_heading',      'Contact');

-- 3e. Contact Page Settings
CREATE TABLE IF NOT EXISTS contact_settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(100) UNIQUE NOT NULL,
    setting_value TEXT
);

INSERT INTO contact_settings (setting_key, setting_value) VALUES
('email',       'al.shifahcharityfoundation@gmail.com'),
('phone_1',     '+234 913 856 3760'),
('phone_2',     '+234 706 536 4835'),
('address',     'No. 07, Khadi Bulama Street, Commissioners Quarters, Damaturu, Yobe State, Nigeria'),
('map_embed',   'https://maps.google.com/maps?q=Damaturu,+Yobe+State,+Nigeria&output=embed&z=13'),
('office_hours','Mon - Fri: 9:00 AM – 5:00 PM');

-- 4. Impact Stats
CREATE TABLE IF NOT EXISTS impact_stats (
    id INT AUTO_INCREMENT PRIMARY KEY,
    label VARCHAR(100) NOT NULL,
    stat_value VARCHAR(50) NOT NULL,
    icon VARCHAR(50) NOT NULL,
    order_rank INT DEFAULT 0
);

INSERT INTO impact_stats (label, stat_value, icon, order_rank) VALUES 
('Kids Educated', '1,200+', 'graduation-cap', 1),
('Families Supported', '450', 'users', 2),
('Meals Provided', '50k+', 'hand-helping', 3),
('Communities Reached', '12', 'globe', 4);

-- 5. Objectives (Article 3)
CREATE TABLE IF NOT EXISTS objectives (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    order_rank INT DEFAULT 0
);

INSERT INTO objectives (title, description, order_rank) VALUES 
('Charitable & Humanitarian Services', 'To render services geared toward supporting the needy and less privileged members of the community through provision of basic needs.', 1),
('Essential Field Assistance', 'Providing assistance in health care, nutrition, water sanitation, hygiene, shelter, and agriculture purely on a charitable basis.', 2),
('Sustainability & Mentorship', 'Creating opportunities for orphans, youth, and vulnerable children through dedicated mentorship and empowerment programmes.', 3),
('Vocational Empowerment', 'To empower orphans and the less privileged in society through specialized vocational and skill acquisition programs.', 4),
('Direct Life Support', 'Assisting children in the critical areas of education, food, medication, and relief materials within the limits of resources.', 5);

-- 6. Team (Article 7 - Trustees)
CREATE TABLE IF NOT EXISTS team_members (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    role VARCHAR(100) NOT NULL,
    bio TEXT,
    photo_url VARCHAR(500),
    type ENUM('Trustee', 'Staff', 'Volunteer') DEFAULT 'Trustee',
    order_rank INT DEFAULT 0
);

INSERT INTO team_members (name, role, type, order_rank) VALUES 
('Muhammad Abubakar Abdullahi', 'Chairman', 'Trustee', 1),
('Gumel Mustapha Ali', 'Secretary', 'Trustee', 2),
('Talba Bukar Talba', 'Board Member', 'Trustee', 3);

-- 7. Campaigns
CREATE TABLE IF NOT EXISTS campaigns (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    long_description TEXT,
    goal_amount DECIMAL(15, 2) NOT NULL,
    current_amount DECIMAL(15, 2) DEFAULT 0,
    image_url VARCHAR(255),
    category VARCHAR(50),
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO campaigns (id, title, description, long_description, goal_amount, current_amount, image_url, category) VALUES 
(1, 'Education & Relief Materials', 'Assisting orphans and less privileged children with education, food, and medication.', 'In accordance with Article 3 of our constitution, we provide direct assistance to orphans and less privileged children...', 50000, 32500, 'https://images.unsplash.com/photo-1497633762265-9d179a990aa6?auto=format&fit=crop&q=80&w=800', 'Education'),
(2, 'WASH & Health Infrastructure', 'Sustainable water sanitation, hygiene, and health care services for rural communities.', 'Water, Sanitation, and Hygiene (WASH) are fundamental human rights...', 25000, 18000, 'https://images.unsplash.com/photo-1541544741938-0af808871cc0?auto=format&fit=crop&q=80&w=800', 'Health'),
(3, 'Vocational Skills Empowerment', 'Empowering orphans and youth through vocational training and skill acquisition programs.', 'Beyond immediate relief, we are committed to long-term sustainability...', 40000, 15000, 'https://images.unsplash.com/photo-1513258496099-48168024adb0?auto=format&fit=crop&q=80&w=800', 'Crisis');

-- 8. Campaign Gallery
CREATE TABLE IF NOT EXISTS campaign_gallery (
    id INT AUTO_INCREMENT PRIMARY KEY,
    campaign_id INT,
    image_url VARCHAR(255) NOT NULL,
    FOREIGN KEY (campaign_id) REFERENCES campaigns(id) ON DELETE CASCADE
);

INSERT INTO campaign_gallery (campaign_id, image_url) VALUES 
(1, 'https://images.unsplash.com/photo-1509062522246-3755977927d7?auto=format&fit=crop&q=80&w=600'),
(1, 'https://images.unsplash.com/photo-1453749024858-4bca89bd9edc?auto=format&fit=crop&q=80&w=600'),
(1, 'https://images.unsplash.com/photo-1503676260728-1c00da094a0b?auto=format&fit=crop&q=80&w=600');

-- 9. Blog Posts
CREATE TABLE IF NOT EXISTS blog_posts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    excerpt TEXT,
    content TEXT NOT NULL,
    post_date VARCHAR(50),
    image_url VARCHAR(255),
    category VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO blog_posts (title, excerpt, content, post_date, image_url, category) VALUES 
('Certified by the Corporate Affairs Commission', 'Celebrating our official registration and the formalization of our humanitarian mission.', 'We are thrilled to announce that Al-Shifah Charity Foundation has officially received its certificate of incorporation...', 'Jan 30, 2025', 'https://images.unsplash.com/photo-1531206715517-5c0ba140b2b8?auto=format&fit=crop&q=80&w=600', 'News');

-- 10. Financial Data
CREATE TABLE IF NOT EXISTS financial_data (
    id INT AUTO_INCREMENT PRIMARY KEY,
    fiscal_year VARCHAR(10) NOT NULL,
    category VARCHAR(50) NOT NULL,
    amount DECIMAL(15, 2) NOT NULL,
    usage_context TEXT
);

INSERT INTO financial_data (fiscal_year, category, amount) VALUES 
('2025', 'Programs', 920000),
('2025', 'Fundraising', 110000),
('2025', 'Admin', 35000);

-- 11. Gallery
CREATE TABLE IF NOT EXISTS gallery (
    id INT AUTO_INCREMENT PRIMARY KEY,
    image_url VARCHAR(255) NOT NULL,
    caption VARCHAR(255),
    category VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO gallery (image_url) VALUES 
('https://images.unsplash.com/photo-1488521787991-ed7bbaae773c?auto=format&fit=crop&q=80&w=400'),
('https://images.unsplash.com/photo-1509062522246-3755977927d7?auto=format&fit=crop&q=80&w=400');

-- 12. Donations
CREATE TABLE IF NOT EXISTS donations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    donor_name VARCHAR(100),
    payment_reference VARCHAR(255),
    amount DECIMAL(15, 2) NOT NULL,
    donation_date DATE,
    campaign_id INT,
    user_id INT,
    is_anonymous BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (campaign_id) REFERENCES campaigns(id) ON DELETE SET NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);

INSERT INTO donations (donor_name, amount, donation_date, campaign_id, is_anonymous) VALUES 
('John Doe', 250, '2025-02-15', 1, FALSE),
('Anonymous', 1000, '2025-02-14', 2, TRUE);

-- 13. Messages (Contact Form)
CREATE TABLE IF NOT EXISTS messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    subject VARCHAR(255),
    message TEXT NOT NULL,
    status ENUM('unread', 'read', 'replied') DEFAULT 'unread',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 14. Broadcasts
CREATE TABLE IF NOT EXISTS broadcasts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    subject VARCHAR(255) NOT NULL,
    content TEXT NOT NULL,
    target_group VARCHAR(50) DEFAULT 'all',
    sent_by INT,
    sent_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (sent_by) REFERENCES admins(id) ON DELETE SET NULL
);

-- 15. Campaign Media (Rich Support)
CREATE TABLE IF NOT EXISTS campaign_media (
    id INT AUTO_INCREMENT PRIMARY KEY,
    campaign_id INT,
    media_url VARCHAR(255) NOT NULL,
    media_type ENUM('image', 'video', 'pdf') DEFAULT 'image',
    caption VARCHAR(255),
    FOREIGN KEY (campaign_id) REFERENCES campaigns(id) ON DELETE CASCADE
);

-- 16. Blog Media
CREATE TABLE IF NOT EXISTS blog_media (
    id INT AUTO_INCREMENT PRIMARY KEY,
    post_id INT,
    media_url VARCHAR(255) NOT NULL,
    media_type ENUM('image', 'video', 'pdf') DEFAULT 'image',
    FOREIGN KEY (post_id) REFERENCES blog_posts(id) ON DELETE CASCADE
);
