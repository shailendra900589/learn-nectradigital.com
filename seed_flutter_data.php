<?php
// seed_flutter_data.php: Coordinates loading of all 4 parts of the Flutter curriculum

echo "Loading Part 1 (Chapters 1 - 11: Dart Fundamentals, OOP, Async, Widget Architecture & Lifecycle)...\n";
require_once __DIR__ . '/seed_flutter_part1.php';

echo "Loading Part 2 (Chapters 12 - 23: Core Layouts, Material Design 3, Forms & Theming)...\n";
require_once __DIR__ . '/seed_flutter_part2.php';

echo "Loading Part 3 (Chapters 24 - 35: Navigation, State Management, REST APIs & Animations)...\n";
require_once __DIR__ . '/seed_flutter_part3.php';

echo "Loading Part 4 (Chapters 36 - 47: SQLite, Device Features, Architecture, Testing & Full Capstone Project)...\n";
require_once __DIR__ . '/seed_flutter_part4.php';
