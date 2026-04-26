<?php
require_once 'includes/db.php';
secure_session_start();

if (isset($_GET['q'])) {
    $search_term = trim($_GET['q']);
    
    // Don't search if the string is empty
    if ($search_term === '' || strlen($search_term) > 80) {
        exit;
    }

    // Use wildcards (%) to find partial matches in title or description
    $q = "%" . $search_term . "%";
    
    $stmt = $pdo->prepare("SELECT title, slug FROM courses WHERE title LIKE :title_q OR description LIKE :description_q LIMIT 5");
    $stmt->execute(['title_q' => $q, 'description_q' => $q]);
    $results = $stmt->fetchAll();

    if (count($results) > 0) {
        foreach ($results as $row) {
            // Output clickable links that go straight to the course reading page
            echo '<a href="' . h(course_path($row['slug'])) . '" class="block px-4 py-3 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-700 border-b border-gray-50 last:border-0 transition-colors flex items-center gap-2">';
            echo '<svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>';
            echo h($row['title']);
            echo '</a>';
        }
    } else {
        echo '<div class="px-4 py-3 text-sm text-gray-500 italic text-center">No matching courses found.</div>';
    }
}
?>
