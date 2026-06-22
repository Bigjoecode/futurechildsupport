<?php
require_once __DIR__ . '/common.php';

future_child_support_require_admin();

$totalDonations = future_child_support_fetch_scalar($dbconnec, "SELECT COUNT(*) FROM donations");
$processingDonations = future_child_support_fetch_scalar($dbconnec, "SELECT COUNT(*) FROM donations WHERE status = 'Processing'");
$totalContacts = future_child_support_fetch_scalar($dbconnec, "SELECT COUNT(*) FROM contact_messages");
$newContacts = future_child_support_fetch_scalar($dbconnec, "SELECT COUNT(*) FROM contact_messages WHERE status = 'new'");

$donations = future_child_support_fetch_rows(
    $dbconnec,
    "SELECT item_name, fullname, company_name, country, state, street, phone, email, amount, payment_type, proof, note, transac_id, status, created_at
     FROM donations
     ORDER BY created_at DESC
     LIMIT 50"
);

$contacts = future_child_support_fetch_rows(
    $dbconnec,
    "SELECT full_name, email, phone, subject, message, status, created_at
     FROM contact_messages
     ORDER BY created_at DESC
     LIMIT 50"
);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Dashboard | Future Child Support</title>
    <style>
        :root {
            --bg: #f5efdf;
            --surface: rgba(255, 251, 243, 0.9);
            --surface-strong: #fffdf8;
            --ink: #1f160c;
            --muted: #72614d;
            --accent: #d49a31;
            --accent-strong: #8a5a00;
            --ok: #2f7d45;
            --line: rgba(31, 22, 12, 0.12);
            --shadow: 0 24px 64px rgba(62, 41, 10, 0.12);
        }

        * {
            box-sizing: border-box;
        }

        html {
            scroll-behavior: smooth;
        }

        body {
            margin: 0;
            font-family: "Trebuchet MS", "Gill Sans", sans-serif;
            color: var(--ink);
            background:
                radial-gradient(circle at top left, rgba(212, 154, 49, 0.18), transparent 24%),
                radial-gradient(circle at bottom right, rgba(138, 90, 0, 0.12), transparent 20%),
                linear-gradient(180deg, #f7f1e3 0%, #f3ead8 100%);
        }

        a {
            color: inherit;
        }

        .shell {
            width: min(1180px, calc(100% - 24px));
            margin: 0 auto;
            padding: 24px 0 40px;
        }

        .topbar {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            padding: 18px 20px;
            border: 1px solid var(--line);
            border-radius: 24px;
            background: rgba(255, 252, 247, 0.88);
            backdrop-filter: blur(14px);
            box-shadow: var(--shadow);
            position: sticky;
            top: 12px;
            z-index: 10;
        }

        .brand {
            display: grid;
            gap: 6px;
        }

        .brand small {
            letter-spacing: 0.12em;
            text-transform: uppercase;
            color: var(--accent-strong);
            font-weight: 700;
        }

        .brand h1 {
            margin: 0;
            font-size: clamp(1.6rem, 4vw, 2.5rem);
        }

        .brand p {
            margin: 0;
            color: var(--muted);
            line-height: 1.5;
        }

        .topbar-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }

        .action-link {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 44px;
            padding: 0 16px;
            border-radius: 999px;
            border: 1px solid var(--line);
            background: var(--surface-strong);
            text-decoration: none;
            font-weight: 700;
        }

        .action-link.primary {
            border: 0;
            color: white;
            background: linear-gradient(135deg, #d89d2f, #8a5a00);
            box-shadow: 0 16px 30px rgba(138, 90, 0, 0.18);
        }

        .stats {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 16px;
            margin-top: 22px;
        }

        .stat-card,
        .panel,
        .entry-card {
            border: 1px solid var(--line);
            border-radius: 24px;
            background: var(--surface);
            box-shadow: var(--shadow);
        }

        .stat-card {
            padding: 20px;
        }

        .stat-card span {
            display: inline-block;
            margin-bottom: 10px;
            color: var(--muted);
            font-size: 14px;
        }

        .stat-card strong {
            display: block;
            font-size: clamp(1.7rem, 4vw, 2.5rem);
        }

        .content-grid {
            display: grid;
            grid-template-columns: 1.25fr 1fr;
            gap: 18px;
            margin-top: 22px;
        }

        .panel {
            padding: 20px;
        }

        .panel-header {
            display: flex;
            flex-wrap: wrap;
            align-items: end;
            justify-content: space-between;
            gap: 10px;
            margin-bottom: 18px;
        }

        .panel-header h2 {
            margin: 0;
            font-size: 1.4rem;
        }

        .panel-header p {
            margin: 6px 0 0;
            color: var(--muted);
        }

        .entry-list {
            display: grid;
            gap: 14px;
        }

        .entry-card {
            padding: 18px;
            background: var(--surface-strong);
        }

        .entry-top {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            gap: 12px;
            margin-bottom: 12px;
        }

        .entry-title {
            margin: 0;
            font-size: 1.1rem;
        }

        .entry-subtitle {
            margin: 5px 0 0;
            color: var(--muted);
            font-size: 14px;
        }

        .badge {
            display: inline-flex;
            align-items: center;
            min-height: 32px;
            padding: 0 12px;
            border-radius: 999px;
            background: rgba(212, 154, 49, 0.16);
            color: var(--accent-strong);
            font-size: 13px;
            font-weight: 700;
        }

        .badge.success {
            background: rgba(47, 125, 69, 0.12);
            color: var(--ok);
        }

        .entry-meta {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 10px 14px;
            margin: 14px 0 0;
        }

        .entry-meta div,
        .entry-body {
            padding: 12px 14px;
            border-radius: 18px;
            background: rgba(245, 239, 223, 0.74);
        }

        .entry-meta span,
        .entry-body span {
            display: block;
            margin-bottom: 6px;
            color: var(--muted);
            font-size: 12px;
            letter-spacing: 0.04em;
            text-transform: uppercase;
        }

        .entry-body {
            margin-top: 12px;
            line-height: 1.6;
            white-space: pre-wrap;
        }

        .proof-link {
            display: inline-flex;
            margin-top: 8px;
            color: var(--accent-strong);
            font-weight: 700;
            text-decoration: none;
        }

        .empty-state {
            border-radius: 20px;
            border: 1px dashed var(--line);
            padding: 20px;
            color: var(--muted);
            background: rgba(255, 255, 255, 0.42);
        }

        @media (max-width: 980px) {
            .stats,
            .content-grid {
                grid-template-columns: 1fr 1fr;
            }
        }

        @media (max-width: 720px) {
            .shell {
                width: min(100%, calc(100% - 18px));
                padding-top: 14px;
            }

            .topbar {
                border-radius: 20px;
                padding: 16px;
            }

            .stats,
            .content-grid,
            .entry-meta {
                grid-template-columns: 1fr;
            }

            .panel,
            .entry-card,
            .stat-card {
                border-radius: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="shell">
        <header class="topbar">
            <div class="brand">
                <small>Future Child Support</small>
                <h1>Admin Dashboard</h1>
                <p>Website submissions now land here instead of being sent out by email.</p>
            </div>
            <div class="topbar-actions">
                <a class="action-link" href="#donations">Donations</a>
                <a class="action-link" href="#contacts">Contacts</a>
                <a class="action-link primary" href="logout.php">Log out</a>
            </div>
        </header>

        <section class="stats">
            <article class="stat-card">
                <span>Total donations</span>
                <strong><?php echo $totalDonations; ?></strong>
            </article>
            <article class="stat-card">
                <span>Processing donations</span>
                <strong><?php echo $processingDonations; ?></strong>
            </article>
            <article class="stat-card">
                <span>Contact messages</span>
                <strong><?php echo $totalContacts; ?></strong>
            </article>
            <article class="stat-card">
                <span>New contacts</span>
                <strong><?php echo $newContacts; ?></strong>
            </article>
        </section>

        <section class="content-grid">
            <div class="panel" id="donations">
                <div class="panel-header">
                    <div>
                        <h2>Recent Donations</h2>
                        <p>Latest 50 donation submissions from the site.</p>
                    </div>
                </div>

                <?php if (empty($donations)): ?>
                    <div class="empty-state">No donations have been saved yet.</div>
                <?php else: ?>
                    <div class="entry-list">
                        <?php foreach ($donations as $donation): ?>
                            <article class="entry-card">
                                <div class="entry-top">
                                    <div>
                                        <h3 class="entry-title"><?php echo future_child_support_format_value($donation['fullname']); ?></h3>
                                        <p class="entry-subtitle"><?php echo future_child_support_format_value($donation['item_name']); ?> • <?php echo future_child_support_format_datetime($donation['created_at']); ?></p>
                                    </div>
                                    <span class="badge"><?php echo future_child_support_format_value($donation['status'], 'Pending'); ?></span>
                                </div>

                                <div class="entry-meta">
                                    <div><span>Amount</span><?php echo future_child_support_format_value($donation['amount']); ?></div>
                                    <div><span>Payment Type</span><?php echo future_child_support_format_value($donation['payment_type']); ?></div>
                                    <div><span>Email</span><?php echo future_child_support_format_value($donation['email']); ?></div>
                                    <div><span>Phone</span><?php echo future_child_support_format_value($donation['phone']); ?></div>
                                    <div><span>Transaction ID</span><?php echo future_child_support_format_value($donation['transac_id']); ?></div>
                                    <div><span>Location</span><?php echo future_child_support_compose_location($donation['country'] ?? '', $donation['state'] ?? ''); ?></div>
                                    <div><span>Street</span><?php echo future_child_support_format_value($donation['street']); ?></div>
                                    <div><span>Company</span><?php echo future_child_support_format_value($donation['company_name'], 'Not Provided'); ?></div>
                                </div>

                                <div class="entry-body">
                                    <span>Donor Note</span>
                                    <?php echo future_child_support_format_value($donation['note'], 'No note provided.'); ?>

                                    <?php if (!empty($donation['proof']) && strtoupper((string) $donation['proof']) !== 'N/A'): ?>
                                        <a class="proof-link" href="<?php echo '../secured/uploads/' . rawurlencode($donation['proof']); ?>" target="_blank" rel="noopener noreferrer">View proof of payment</a>
                                    <?php endif; ?>
                                </div>
                            </article>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <div class="panel" id="contacts">
                <div class="panel-header">
                    <div>
                        <h2>Contact Messages</h2>
                        <p>Latest 50 website contact submissions.</p>
                    </div>
                </div>

                <?php if (empty($contacts)): ?>
                    <div class="empty-state">No contact submissions have been saved yet.</div>
                <?php else: ?>
                    <div class="entry-list">
                        <?php foreach ($contacts as $contact): ?>
                            <article class="entry-card">
                                <div class="entry-top">
                                    <div>
                                        <h3 class="entry-title"><?php echo future_child_support_format_value($contact['full_name']); ?></h3>
                                        <p class="entry-subtitle"><?php echo future_child_support_format_datetime($contact['created_at']); ?></p>
                                    </div>
                                    <span class="badge success"><?php echo future_child_support_format_value($contact['status'], 'new'); ?></span>
                                </div>

                                <div class="entry-meta">
                                    <div><span>Email</span><?php echo future_child_support_format_value($contact['email']); ?></div>
                                    <div><span>Phone</span><?php echo future_child_support_format_value($contact['phone'], 'Not Provided'); ?></div>
                                    <div><span>Subject</span><?php echo future_child_support_format_value($contact['subject']); ?></div>
                                    <div><span>Status</span><?php echo future_child_support_format_value($contact['status']); ?></div>
                                </div>

                                <div class="entry-body">
                                    <span>Message</span>
                                    <?php echo future_child_support_format_value($contact['message']); ?>
                                </div>
                            </article>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </section>
    </div>
</body>
</html>
