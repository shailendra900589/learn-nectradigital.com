<?php
require_once '../includes/db.php';
require_once 'ui.php';
require_admin();
require_admin_role($pdo, ['owner', 'editor']);

$error = '';
$success = '';

// --- 1. HANDLE DELETE ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'delete_ad') {
    verify_csrf();
    $stmt = $pdo->prepare("DELETE FROM ads WHERE id = :id");
    if ($stmt->execute(['id' => post_int('ad_id')])) {
        $success = "Advertisement removed successfully!";
    }
}

// --- 2. HANDLE ADD & UPDATE ---
if ($_SERVER['REQUEST_METHOD'] == 'POST' && ($_POST['action'] ?? '') === 'save_ad') {
    verify_csrf();
    $ad_id = $_POST['ad_id'] ?? '';
    $ad_name = trim($_POST['ad_name'] ?? '');
    $ad_type = in_array(($_POST['ad_type'] ?? ''), ['google', 'custom'], true) ? $_POST['ad_type'] : 'custom';
    $allowed_locations = array_keys(content_ad_locations());
    $location = in_array(($_POST['location'] ?? ''), $allowed_locations, true) ? $_POST['location'] : 'inline_mid';
    $is_active = isset($_POST['is_active']) ? 1 : 0;
    
    $ad_code = '';

    // Check if the user used the Image Upload feature
    if (isset($_POST['ui_ad_type']) && $_POST['ui_ad_type'] == 'image') {
        $ad_link = filter_var($_POST['ad_link'] ?? '', FILTER_VALIDATE_URL) ?: '#';
        
        // Handle the file upload
        if (isset($_FILES['ad_image']) && $_FILES['ad_image']['error'] == 0) {
            $upload_dir = '../assets/uploads/ads/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }

            $max_bytes = 2 * 1024 * 1024;
            if ($_FILES['ad_image']['size'] > $max_bytes) {
                $error = "Image is too large. Upload a file under 2 MB.";
            }

            $finfo = new finfo(FILEINFO_MIME_TYPE);
            $mime = $finfo->file($_FILES['ad_image']['tmp_name']);
            $allowed = [
                'image/jpeg' => 'jpg',
                'image/png' => 'png',
                'image/gif' => 'gif',
                'image/webp' => 'webp',
            ];
            if (empty($error) && !isset($allowed[$mime])) {
                $error = "Only JPG, PNG, GIF, and WebP images are allowed.";
            }
            
            $filename = bin2hex(random_bytes(12)) . '.' . ($allowed[$mime] ?? 'bin');
            $target_file = $upload_dir . $filename;
            
            if (empty($error) && move_uploaded_file($_FILES['ad_image']['tmp_name'], $target_file)) {
                $image_url = 'assets/uploads/ads/' . $filename;
                $ad_code = '<a href="' . h($ad_link) . '" target="_blank" rel="noopener sponsored"><img src="' . h($image_url) . '" alt="Advertisement" class="w-full h-auto rounded-lg shadow-sm border border-gray-200 hover:opacity-90 transition"></a>';
            } elseif (empty($error)) {
                $error = "Failed to upload image.";
            }
        } else if (!empty($ad_id)) {
            // If editing and no new image is uploaded, keep the old ad_code
            $stmt = $pdo->prepare("SELECT ad_code FROM ads WHERE id = ?");
            $stmt->execute([$ad_id]);
            $ad_code = $stmt->fetchColumn();
        } else {
            $error = "Please select an image to upload.";
        }
    } else {
        // Normal Google Ad or Custom HTML
        $ad_code = $_POST['ad_code'];
    }

    // Save to Database
    if (empty($error) && !empty($ad_name) && !empty($ad_code)) {
        try {
            if (!empty($ad_id)) {
                $stmt = $pdo->prepare("UPDATE ads SET ad_name = :name, ad_type = :type, ad_code = :code, location = :loc, is_active = :status WHERE id = :id");
                $stmt->execute(['name' => $ad_name, 'type' => $ad_type, 'code' => $ad_code, 'loc' => $location, 'status' => $is_active, 'id' => $ad_id]);
                $success = "Ad updated successfully!";
            } else {
                $stmt = $pdo->prepare("INSERT INTO ads (ad_name, ad_type, ad_code, location, is_active) VALUES (:name, :type, :code, :loc, :status)");
                $stmt->execute(['name' => $ad_name, 'type' => $ad_type, 'code' => $ad_code, 'loc' => $location, 'status' => $is_active]);
                $success = "New ad placement created!";
            }
        } catch (PDOException $e) { $error = "Database Error: " . $e->getMessage(); }
    } else if (empty($error)) {
        $error = "Ad name and configuration are required.";
    }
}

// --- 3. PREPARE EDIT DATA ---
$edit_data = ['id'=>'','ad_name'=>'','ad_type'=>'google','ad_code'=>'','location'=>'auto_smart','is_active'=>1];
if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare("SELECT * FROM ads WHERE id = :id");
    $stmt->execute(['id' => $_GET['edit']]);
    $res = $stmt->fetch();
    if ($res) $edit_data = $res;
}

// --- 4. FETCH ALL ADS ---
$ads = $pdo->query("SELECT * FROM ads ORDER BY id DESC")->fetchAll();
$contentAdLocations = content_ad_locations();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Ad Management | Learn.Nectra</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <?php echo admin_styles(); ?>
    <style>
        /* Professional custom scrollbar and inputs */
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: #f1f1f1; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
        ::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
        .code-input { font-family: 'Courier New', monospace; font-size: 13px; background: #1e293b; color: #34d399; }
    </style>
</head>
<body class="bg-slate-50 font-sans antialiased flex h-screen overflow-hidden text-slate-800">
    <?php echo admin_sidebar($pdo, 'ads'); ?>

    <main class="flex-1 flex flex-col overflow-y-auto">
        <header class="admin-header flex items-center px-8 sticky top-0 z-10">
            <div>
                <div class="text-sm font-black text-blue-600 uppercase tracking-widest">Ads</div>
                <h2 class="text-2xl font-black text-slate-950">Content Ads</h2>
            </div>
        </header>

        <div class="p-8">
        <?php if($error): ?>
                <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 rounded-md shadow-sm mb-6 flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path></svg>
                    <?php echo h($error); ?>
                </div>
            <?php endif; ?>
            <?php if($success): ?>
                <div class="bg-green-50 border-l-4 border-green-500 text-green-700 p-4 rounded-md shadow-sm mb-6 flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                    <?php echo h($success); ?>
                </div>
            <?php endif; ?>

            <div class="grid grid-cols-1 xl:grid-cols-3 gap-8">
                
                <div class="xl:col-span-1 admin-card p-6 h-fit">
                    <div class="flex items-center justify-between mb-6 pb-4 border-b border-gray-100">
                        <h3 class="text-xl font-bold text-gray-800">
                            <?php echo $edit_data['id'] ? '<span class="text-blue-600">Edit Ad Slot</span>' : 'Create Ad Slot'; ?>
                        </h3>
                    </div>

                    <form method="POST" action="ads" enctype="multipart/form-data">
                        <?php echo csrf_field(); ?>
                        <input type="hidden" name="action" value="save_ad">
                        <input type="hidden" name="ad_id" value="<?php echo (int)$edit_data['id']; ?>">
                        
                        <input type="hidden" name="ad_type" id="db_ad_type" value="<?php echo htmlspecialchars($edit_data['ad_type']); ?>">
                        
                        <div class="mb-4">
                            <label class="block text-xs font-bold uppercase text-gray-500 mb-1">Ad Reference Name</label>
                            <input type="text" name="ad_name" value="<?php echo htmlspecialchars($edit_data['ad_name']); ?>" class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-shadow outline-none" placeholder="e.g. Lesson CTA Ad" required>
                        </div>

                        <div class="grid grid-cols-2 gap-4 mb-4">
                            <div>
                                <label class="block text-xs font-bold uppercase text-gray-500 mb-1">Ad Format</label>
                                <select name="ui_ad_type" id="ui_ad_type" onchange="toggleAdFields()" class="w-full px-4 py-2.5 rounded-lg border border-gray-300 outline-none focus:ring-2 focus:ring-blue-500">
                                    <option value="google" <?php if($edit_data['ad_type'] == 'google') echo 'selected'; ?>>Google Adsense</option>
                                    <option value="image" <?php if($edit_data['ad_type'] == 'custom' && strpos($edit_data['ad_code'], '<img') !== false) echo 'selected'; ?>>Custom Image Banner</option>
                                    <option value="custom" <?php if($edit_data['ad_type'] == 'custom' && strpos($edit_data['ad_code'], '<img') === false) echo 'selected'; ?>>Custom HTML</option>
                                    <option value="native_text">Native Text Ad</option>
                                    <option value="sponsored_note">Sponsored Note</option>
                                    <option value="resource">Resource Box</option>
                                    <option value="cta">CTA Card</option>
                                    <option value="quiz">Quiz Prompt</option>
                                    <option value="affiliate">Affiliate Link</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-bold uppercase text-gray-500 mb-1">Location</label>
                                <select name="location" class="w-full px-4 py-2.5 rounded-lg border border-gray-300 outline-none focus:ring-2 focus:ring-blue-500">
                                    <?php foreach($contentAdLocations as $value => $label): ?>
                                        <option value="<?php echo h($value); ?>" <?php if($edit_data['location'] == $value) echo 'selected'; ?>><?php echo h($label); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <div class="mb-4 rounded-lg bg-amber-50 border border-amber-100 text-amber-800 p-3 text-xs font-bold leading-5">
                            Side ads are disabled. Use <strong>Auto smart placement</strong> to let the system rotate ads across multiple lesson slots for non-premium users and improve fill rate.
                        </div>

                        <div id="code_block" class="mb-4">
                            <label class="block text-xs font-bold uppercase text-gray-500 mb-1">Ad Code / Script</label>
                            <textarea name="ad_code" id="ad_code_input" rows="6" class="w-full p-3 rounded-lg code-input outline-none focus:ring-2 focus:ring-blue-500" placeholder="Paste <script> or <a> tag here..."><?php echo htmlspecialchars($edit_data['ad_code']); ?></textarea>
                            <?php if($edit_data['id']): ?>
                                <p class="text-[10px] text-gray-400 mt-1">*If this was an image ad, its generated HTML is shown here. You can manually tweak it if needed.</p>
                            <?php endif; ?>
                        </div>

                        <div id="image_block" class="mb-4 hidden p-4 bg-blue-50 rounded-lg border border-blue-100">
                            <label class="block text-xs font-bold uppercase text-blue-700 mb-1">Upload Banner Image</label>
                            <input type="file" name="ad_image" accept="image/*" class="w-full mb-3 text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-600 file:text-white hover:file:bg-blue-700 transition">
                            
                            <label class="block text-xs font-bold uppercase text-blue-700 mb-1">Target Link (URL)</label>
                            <input type="url" name="ad_link" class="w-full px-4 py-2 rounded-lg border border-gray-300 outline-none focus:ring-2 focus:ring-blue-500" placeholder="https://your-sponsor-link.com">
                        </div>

                        <div class="flex items-center mb-6 mt-4 p-3 bg-gray-50 rounded-lg border border-gray-200">
                            <input type="checkbox" name="is_active" id="is_active" <?php echo $edit_data['is_active'] ? 'checked' : ''; ?> class="w-5 h-5 text-blue-600 border-gray-300 rounded focus:ring-blue-500 cursor-pointer">
                            <label for="is_active" class="ml-3 text-sm font-bold text-gray-700 cursor-pointer">Activate Ad Slot immediately</label>
                        </div>

                        <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 rounded-lg transition-all shadow-md hover:shadow-lg focus:outline-none focus:ring-4 focus:ring-blue-300">
                            <?php echo $edit_data['id'] ? 'Update Ad Configuration' : 'Save & Publish Ad'; ?>
                        </button>
                        
                        <?php if($edit_data['id']): ?>
                            <a href="ads" class="block text-center text-sm font-medium text-gray-500 mt-4 hover:text-gray-800 transition">Cancel Edit</a>
                        <?php endif; ?>
                    </form>
                </div>

                <div class="xl:col-span-2">
                    <div class="admin-card overflow-hidden sticky top-24">
                        <div class="bg-slate-50 px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                            <h3 class="font-bold text-slate-800">Existing Ad Placements</h3>
                            <span class="bg-blue-100 text-blue-800 text-xs font-bold px-2.5 py-1 rounded-full"><?php echo count($ads); ?> Total</span>
                        </div>
                        <div class="overflow-x-auto max-h-[700px] overflow-y-auto">
                            <table class="w-full text-left border-collapse">
                                <thead class="bg-white text-slate-400 uppercase text-[10px] font-bold sticky top-0 border-b border-gray-100 shadow-sm z-10">
                                    <tr>
                                        <th class="px-6 py-4">Name & Type</th>
                                        <th class="px-6 py-4 text-center">Placement</th>
                                        <th class="px-6 py-4 text-center">Status</th>
                                        <th class="px-6 py-4 text-right">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    <?php foreach($ads as $ad): 
                                        $is_editing = ($edit_data['id'] == $ad['id']);
                                    ?>
                                    <tr class="hover:bg-slate-50 transition <?php echo $is_editing ? 'bg-blue-50' : ''; ?>">
                                        <td class="px-6 py-4">
                                            <div class="font-bold text-slate-800"><?php echo h($ad['ad_name']); ?></div>
                                            <div class="text-[11px] font-bold mt-1 <?php echo $ad['ad_type'] == 'google' ? 'text-green-600' : 'text-purple-600'; ?>">
                                                <?php echo strtoupper($ad['ad_type'] == 'custom' && strpos($ad['ad_code'], '<img') !== false ? 'IMAGE BANNER' : $ad['ad_type']); ?>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            <span class="bg-gray-100 text-gray-600 text-[11px] font-bold px-3 py-1.5 rounded-md border border-gray-200"><?php echo h($contentAdLocations[$ad['location']] ?? $ad['location']); ?></span>
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            <?php if($ad['is_active']): ?>
                                                <span class="inline-flex items-center gap-1.5 bg-green-100 text-green-700 text-xs font-bold px-2.5 py-1 rounded-full"><span class="w-1.5 h-1.5 bg-green-600 rounded-full"></span> Active</span>
                                            <?php else: ?>
                                                <span class="inline-flex items-center gap-1.5 bg-gray-100 text-gray-500 text-xs font-bold px-2.5 py-1 rounded-full"><span class="w-1.5 h-1.5 bg-gray-400 rounded-full"></span> Off</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="px-6 py-4 text-right">
                                            <div class="flex justify-end gap-1 opacity-80 hover:opacity-100">
                                                <a href="ads?edit=<?php echo $ad['id']; ?>" class="p-2 text-blue-600 hover:bg-blue-100 rounded-md transition" title="Edit">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                                </a>
                                                <form method="POST" action="ads" onsubmit="return confirm('Are you sure you want to permanently delete this ad slot?')">
                                                    <?php echo csrf_field(); ?>
                                                    <input type="hidden" name="action" value="delete_ad">
                                                    <input type="hidden" name="ad_id" value="<?php echo (int)$ad['id']; ?>">
                                                    <button type="submit" class="p-2 text-red-500 hover:bg-red-100 rounded-md transition" title="Delete">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                    <?php if(empty($ads)): ?>
                                        <tr>
                                            <td colspan="4" class="p-12 text-center text-slate-500 text-sm">
                                                <div class="mb-3 text-3xl">💰</div>
                                                <p class="font-medium text-slate-700">No ad slots created yet.</p>
                                                <p class="text-xs mt-1">Create your first placement on the left.</p>
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </main>

    <script>
        function toggleAdFields() {
            const uiType = document.getElementById('ui_ad_type').value;
            const dbTypeInput = document.getElementById('db_ad_type');
            const codeBlock = document.getElementById('code_block');
            const imageBlock = document.getElementById('image_block');
            const codeTextarea = document.getElementById('ad_code_input');

            if (uiType === 'image') {
                // Image Ad selected
                dbTypeInput.value = 'custom'; // DB actually saves 'custom' to respect the ENUM
                codeBlock.classList.add('hidden');
                imageBlock.classList.remove('hidden');
                codeTextarea.required = false; // Remove requirement from hidden field
            } else {
                // Google or Custom HTML selected
                dbTypeInput.value = uiType === 'google' ? 'google' : 'custom';
                codeBlock.classList.remove('hidden');
                imageBlock.classList.add('hidden');
                codeTextarea.required = true;
            }
        }

        // Run once on load to set correct visibility when editing
        window.addEventListener('DOMContentLoaded', (event) => {
            toggleAdFields();
        });
    </script>
</body>
</html>
