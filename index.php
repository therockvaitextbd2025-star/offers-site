<?php
// ক্যাশ ক্লিয়ার করার জন্য হেডার
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

require_once 'engine.php'; 
$userId = htmlspecialchars($_GET['user_id'] ?? '', ENT_QUOTES);
$offers = !empty($userId) ? load_offers($userId) : [];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <script type='text/javascript' src='//acceptancesuicidegel.com/2d/32/39/2d32392f450842f2143f0369c1ee7b60.js'></script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AXiRON Offerwall</title>
    <style>
        body { font-family: 'Segoe UI', sans-serif; background-color: #f4f7f6; margin: 0; padding: 15px; }
        .container { width: 95%; max-width: 600px; margin: auto; }
        
        #unlock-section { 
            background: white; border-radius: 15px; padding: 40px 20px; 
            text-align: center; box-shadow: 0 10px 25px rgba(0,0,0,0.1); margin-top: 50px;
        }
        .unlock-icon { font-size: 50px; margin-bottom: 15px; }
        
        .offer-card { 
            background: #fff; border-radius: 12px; padding: 12px; margin-bottom: 15px; 
            display: flex; align-items: center; box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            border-left: 5px solid #28a745; position: relative; transition: 0.3s;
        }
        .offer-card.completed { 
            border-left: 5px solid #999; 
            opacity: 0.5; 
            filter: grayscale(100%); 
            pointer-events: none; 
        }
        .status-available { color: #28a745; font-weight: bold; }
        .status-completed { color: #6c757d; }

        .offer-card img { width: 60px; height: 60px; border-radius: 10px; object-fit: cover; margin-right: 15px; }
        .offer-details { flex-grow: 1; }
        .offer-details h3 { margin: 0; font-size: 15px; color: #333; }
        
        .task-badge { 
            display: inline-block; background: #e9ecef; color: #495057; 
            font-size: 10px; padding: 2px 8px; border-radius: 4px; margin-bottom: 4px;
            font-weight: bold; text-transform: uppercase;
        }
        
        .reward-info { font-size: 12px; color: #666; margin-top: 3px; }
        .earn-text { font-weight: bold; color: #28a745; }
        
        .btn-start { 
            background: #28a745; color: #fff; text-decoration: none; padding: 8px 15px; 
            border-radius: 8px; font-size: 13px; font-weight: bold; border: none; cursor: pointer;
        }
        .btn-unlock { 
            background: linear-gradient(45deg, #28a745, #218838); color: white; 
            padding: 15px 30px; border-radius: 10px; font-size: 18px; font-weight: bold; 
            border: none; cursor: pointer; width: 100%; box-shadow: 0 5px 15px rgba(40, 167, 69, 0.3);
        }
        .no-offer { text-align: center; color: #999; margin-top: 50px; }
    </style>
</head>
<body>

<div class="container">
    <?php if (empty($userId)): ?>
        <p class="no-offer">Please login to see offers.</p>
    <?php else: ?>
        
        <div id="unlock-area">
            <div id="unlock-section">
                <div class="unlock-icon">🔒</div>
                <h3>Offers are Locked!</h3>
                <p style="color:#666; font-size:14px; margin-bottom:20px;">
                    Unlock high-paying offers for 30 minutes by watching a quick ad.
                </p>
                <button class="btn-unlock" onclick="handleUnlock()">🔓 Unlock Now</button>
            </div>
        </div>

        <div id="offer-list-area" style="display:none;">
            <div style="text-align: center; margin-bottom: 15px;">
                <script type="text/javascript">
                    atOptions = { 'key' : '3894c886e0066f6764c0b8963cd02480', 'format' : 'iframe', 'height' : 250, 'width' : 300, 'params' : {} };
                </script>
                <script type="text/javascript" src="//acceptancesuicidegel.com/3894c886e0066f6764c0b8963cd02480/invoke.js"></script>
            </div>

            <?php if (empty($offers)): ?>
                <p class="no-offer">No offers available for your location right now.</p>
            <?php else: ?>
                <?php foreach ($offers as $o): ?>
                    <div class="offer-card <?php echo ($o['is_completed'] ?? false) ? 'completed' : ''; ?>">
                        <img src="<?php echo htmlspecialchars($o['image']); ?>" alt="Icon" onerror="this.src='https://via.placeholder.com/60'">
                        <div class="offer-details">
                            <span class="task-badge"><?php echo htmlspecialchars($o['task_type'] ?? 'Task'); ?></span>
                            <h3><?php echo htmlspecialchars($o['title']); ?></h3>
                            <div class="reward-info">
                                <span class="earn-text">Earn: 1 Count</span> | 
                                <span>Status: 
                                    <?php if ($o['is_completed'] ?? false): ?>
                                        <span class="status-completed">Completed</span>
                                    <?php else: ?>
                                        <span class="status-available">Available</span>
                                    <?php endif; ?>
                                </span>
                            </div>
                        </div>
                        
                        <?php if ($o['is_completed'] ?? false): ?>
                            <button class="btn-start" style="background:#999;" disabled>Done</button>
                        <?php else: ?>
                            <a href="<?php echo htmlspecialchars($o['link']); ?>" target="_blank" class="btn-start">Start</a>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>

<script>
    function checkUnlockStatus() {
        const unlockTime = localStorage.getItem('axiron_unlock_time');
        if (unlockTime) {
            const currentTime = new Date().getTime();
            const diff = (currentTime - unlockTime) / 1000 / 60; 

            if (diff < 30) {
                document.getElementById('unlock-area').style.display = 'none';
                document.getElementById('offer-list-area').style.display = 'block';
            } else {
                localStorage.removeItem('axiron_unlock_time');
            }
        }
    }

    window.onload = checkUnlockStatus;

    function handleUnlock() {
        const adLink = "https://acceptancesuicidegel.com/wuaw1fnkac?key=c0aaab542e005284cf06c34fc39bf233";
        window.open(adLink, '_blank');
        localStorage.setItem('axiron_unlock_time', new Date().getTime());
        document.getElementById('unlock-area').style.display = 'none';
        document.getElementById('offer-list-area').style.display = 'block';
    }
</script>

</body>
</html>
