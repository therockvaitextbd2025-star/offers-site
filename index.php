<?php
require_once 'engine.php'; // engine.php লোড করা হলো
$userId = htmlspecialchars($_GET['user_id'] ?? '', ENT_QUOTES);
$offers = !empty($userId) ? load_offers($userId) : [];
?>

<div id="offers">
    <?php if (empty($offers)): ?>
        <p style='text-align:center;'>No offers right now.</p>
    <?php else: ?>
        <?php foreach ($offers as $o): ?>
            <div class="offer">
                <img src="<?php echo htmlspecialchars($o['image']); ?>" onerror="this.style.display='none'">
                <div class="offer-info">
                    <h3><?php echo htmlspecialchars($o['title']); ?></h3>
                    <p>Reward: $<?php echo htmlspecialchars($o['payout']); ?></p>
                </div>
                <a href="<?php echo htmlspecialchars($o['link']); ?>" target="_blank">Start</a>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>
