-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 15, 2026 at 06:37 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `elp_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `assessment`
--

CREATE TABLE `assessment` (
  `AssessmentID` int(11) NOT NULL,
  `CourseID` int(11) DEFAULT NULL,
  `Title` varchar(255) NOT NULL,
  `Type` varchar(50) DEFAULT NULL,
  `DueDate` datetime DEFAULT NULL,
  `TimeLimit` int(11) DEFAULT NULL,
  `MaxScore` int(11) DEFAULT NULL,
  `Status` enum('active','closed') DEFAULT 'active',
  `CreatedAt` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `assessment`
--

INSERT INTO `assessment` (`AssessmentID`, `CourseID`, `Title`, `Type`, `DueDate`, `TimeLimit`, `MaxScore`, `Status`, `CreatedAt`) VALUES
(10, 3, 'QUZI', 'quiz', NULL, 30, 100, 'active', '2026-04-14 17:38:24'),
(11, 5, 'QUZI CART', 'quiz', NULL, 30, 100, 'active', '2026-04-14 17:40:55'),
(12, 6, 'Machine Learning MID', 'exam', NULL, 60, 45, 'active', '2026-04-14 17:42:36'),
(13, 8, 'Show baked cake', 'assignment', NULL, 180, 65, 'active', '2026-04-14 17:43:18'),
(14, 8, 'QUZI', 'quiz', NULL, 30, 100, 'active', '2026-04-14 17:56:25'),
(15, 9, 'FINAL', 'exam', NULL, 30, 100, 'active', '2026-04-14 18:13:54'),
(17, 11, 'ARK QUZI', 'quiz', NULL, 30, 100, 'active', '2026-04-14 20:54:49'),
(18, 11, 'assignment', 'assignment', NULL, 30, 100, 'active', '2026-04-14 20:55:54');

-- --------------------------------------------------------

--
-- Table structure for table `assessment_submission`
--

CREATE TABLE `assessment_submission` (
  `SubmissionID` int(11) NOT NULL,
  `AssessmentID` int(11) DEFAULT NULL,
  `StudentID` int(11) DEFAULT NULL,
  `SubmissionDate` datetime DEFAULT current_timestamp(),
  `ScoreEarned` int(11) DEFAULT NULL,
  `GradingStatus` varchar(50) DEFAULT NULL,
  `AnswersJSON` text DEFAULT NULL,
  `SubmissionLink` text DEFAULT NULL,
  `Feedback` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `assessment_submission`
--

INSERT INTO `assessment_submission` (`SubmissionID`, `AssessmentID`, `StudentID`, `SubmissionDate`, `ScoreEarned`, `GradingStatus`, `AnswersJSON`, `SubmissionLink`, `Feedback`) VALUES
(6, 10, 3, '2026-04-14 23:45:00', 78, 'graded', '[0,0]', NULL, ''),
(7, 13, 3, '2026-04-14 23:45:32', 65, 'graded', NULL, 'https://drive.com/cake', ''),
(8, 12, 3, '2026-04-14 23:45:54', 45, 'graded', NULL, 'https://drive.com/machine', ''),
(9, 11, 3, '2026-04-14 23:46:05', 100, 'graded', '[0,0]', NULL, NULL),
(10, 14, 3, '2026-04-15 02:18:16', 0, 'graded', '[0]', NULL, NULL),
(12, 17, 3, '2026-04-15 03:04:21', 100, 'graded', '[2]', NULL, NULL),
(13, 15, 3, '2026-04-15 03:04:40', NULL, 'pending', NULL, 'https://drive.com/cake', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `course`
--

CREATE TABLE `course` (
  `CourseID` int(11) NOT NULL,
  `InstructorID` int(11) DEFAULT NULL,
  `Title` varchar(255) NOT NULL,
  `Category` varchar(100) DEFAULT NULL,
  `Level` varchar(50) DEFAULT NULL,
  `Duration` varchar(50) DEFAULT NULL,
  `ThumbnailURL` varchar(255) DEFAULT NULL,
  `Rating` float DEFAULT 0,
  `Status` enum('draft','published','archived') DEFAULT 'draft',
  `CreatedAt` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `course`
--

INSERT INTO `course` (`CourseID`, `InstructorID`, `Title`, `Category`, `Level`, `Duration`, `ThumbnailURL`, `Rating`, `Status`, `CreatedAt`) VALUES
(3, 2, 'Galbrena Build Guide', 'Business', 'Advanced', '0', 'https://external-content.duckduckgo.com/iu/?u=https%3A%2F%2Fgladiatorboost.com%2Fwp-content%2Fuploads%2F2025%2F10%2FWuthering-Waves-Galbrena-Team-Guide.jpg&f=1&nofb=1&ipt=8e4000fa0dadb0ad56321fd73c7d38179a3584130b0bf7ae08dfc9961dc88951', 0, 'published', '2026-04-14 01:23:52'),
(5, 2, 'Cartethiya Build Masterplan', 'Design', 'Intermediate', '0', 'https://img.gurugamer.com/resize/740x-/2025/06/06/maxresdefault-3-cff8.jpg', 0, 'published', '2026-04-14 22:38:18'),
(6, 2, 'Machine Learning 101', 'Programming', 'Beginner', '0', 'https://www.masaischool.com/blog/content/images/size/w2000/2024/12/Artboard-1--2-.png', 0, 'published', '2026-04-14 22:49:26'),
(7, 2, 'Introductory Guide to making memes', 'Business', 'Beginner', '0', 'https://external-content.duckduckgo.com/iu/?u=https%3A%2F%2Fi.kym-cdn.com%2Fentries%2Ficons%2Foriginal%2F000%2F046%2F895%2Fhuh_cat.jpg&f=1&nofb=1&ipt=3e12ede1646141f91c83ec356318147fe9d1594f19624ad6e715a3f7898618db', 0, 'published', '2026-04-14 22:50:44'),
(8, 2, 'Agargaon Cake Business Roadmap - Broke to trillionaire', 'Business', 'Advanced', '0', 'https://external-content.duckduckgo.com/iu/?u=https%3A%2F%2Fcdn.bdnews24.com%2Fbdnews24%2Fmedia%2Fbangla%2FimgAll%2F2025October%2Ffootpath-cake-shop-agargaon-051025-048-1759676779.jpg&f=1&nofb=1&ipt=055492b18a6f219a95a805bb95f9bd2b0d81cf9c55c1d9b5fc10ab10', 0, 'published', '2026-04-14 22:53:41'),
(9, 2, 'Learn PyTorch with Saul Goodman', 'Data Science', 'Advanced', '0', 'https://www.digibeatrix.com/python/wp-content/uploads/2024/12/80ae074c489d9507b186e75bb48f23b5-1024x585.webp', 0, 'published', '2026-04-14 23:10:01'),
(11, 2, 'Arknight Tutorial', 'Business', 'Advanced', '0', 'https://cdn2.unrealengine.com/arknights-endfield-key-3840x2160-58c8d21aa303.jpg', 0, 'published', '2026-04-15 02:50:32');

-- --------------------------------------------------------

--
-- Table structure for table `enrollment`
--

CREATE TABLE `enrollment` (
  `EnrollmentID` int(11) NOT NULL,
  `StudentID` int(11) DEFAULT NULL,
  `CourseID` int(11) DEFAULT NULL,
  `EnrollmentDate` datetime DEFAULT current_timestamp(),
  `ProgressPercentage` float DEFAULT 0,
  `IsCompleted` tinyint(1) DEFAULT 0,
  `CertificateIssued` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `enrollment`
--

INSERT INTO `enrollment` (`EnrollmentID`, `StudentID`, `CourseID`, `EnrollmentDate`, `ProgressPercentage`, `IsCompleted`, `CertificateIssued`) VALUES
(3, 5, 3, '2026-04-14 01:35:17', 0, 0, 0),
(4, 3, 3, '2026-04-14 01:46:30', 100, 1, 0),
(5, 3, 9, '2026-04-14 23:44:09', 100, 1, 0),
(6, 3, 8, '2026-04-14 23:44:11', 100, 1, 0),
(7, 3, 7, '2026-04-14 23:44:12', 66.6667, 0, 0),
(8, 3, 6, '2026-04-14 23:44:13', 100, 1, 1),
(9, 3, 5, '2026-04-14 23:44:14', 100, 1, 0),
(11, 3, 11, '2026-04-15 02:57:52', 50, 0, 1);

-- --------------------------------------------------------

--
-- Table structure for table `lesson`
--

CREATE TABLE `lesson` (
  `LessonID` int(11) NOT NULL,
  `ModuleID` int(11) DEFAULT NULL,
  `Title` varchar(255) NOT NULL,
  `ContentType` enum('video','text','file') NOT NULL,
  `ContentURL` varchar(255) DEFAULT NULL,
  `TextBody` text DEFAULT NULL,
  `FileURL` varchar(255) DEFAULT NULL,
  `SequenceOrder` int(11) DEFAULT NULL,
  `CreatedAt` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `lesson`
--

INSERT INTO `lesson` (`LessonID`, `ModuleID`, `Title`, `ContentType`, `ContentURL`, `TextBody`, `FileURL`, `SequenceOrder`, `CreatedAt`) VALUES
(3, 4, 'Intro', 'video', 'https://www.youtube.com/watch?v=7tFW4r7XYxk', '', NULL, NULL, '2026-04-14 17:18:48'),
(4, 5, 'Course Lesson: Mastering Galbrena (Fusion Main DPS)', 'text', 'https://www.youtube.com/watch?v=7tFW4r7XYxk', 'Galbrena is a powerhouse 5-star Fusion Resonator who uses Pistols to dominate the battlefield. Her playstyle revolves around a high-octane transformation mechanic where she shifts from her standard state into a devastating \"Demon\" form.\n\n---\n\n### I. Core Mechanics & Forte Circuit\nTo play Galbrena effectively, you must manage two unique resources: **Sinflame** and **Afterflame**.\n\n* **Sinflame (The Right Bar):** Generated by Galbrena’s own attacks (Basic, Heavy, and Resonance Skill). When this hits 100, your Resonance Skill transforms into **Ascent of Malice**, allowing you to enter **Demon Hypostasis** mode.\n* **Afterflame (The Left Bar):** Generated when *other* team members cast their Echo Skills. This bar is consumed the moment you enter Demon form to provide a massive damage multiplier.\n* **Hellstride (Special Dodge):** While in Demon form, Galbrena has a unique dodge that does not reset her attack combo, allowing for relentless pressure.\n\n---\n\n### II. Best Gear & Stats\n\n#### 1. Weapons\n* **Best in Slot:** *Lux & Umbra* (Signature 5-star). It provides Crit DMG and massive buffs to Heavy Attack and Echo Skill damage, plus DEF ignore.\n* **Premium Alternative:** *Static Mist* (Standard 5-star). A solid stat stick for Crit Rate.\n* **Best 4-star:** *Solar Flame*. At high refinements, this is her best non-5-star option due to the Heavy Attack DMG bonuses.\n\n#### 2. Echoes\n* **Best Set:** **Flamewing\'s Shadow** (5-piece). \n    * *Bonus:* Greatly increases Fusion DMG and provides Crit Rate buffs when using Echo and Heavy attacks.\n* **Main Echo (Cost 4):** **Corrosaurus**.\n    * *Why:* It specifically buffs Fusion and Echo Skill damage, perfectly aligning with her kit.\n* **Stat Priority:** 1.  **Crit Rate / Crit DMG** (Aim for a 1:2 ratio).\n    2.  **Energy Regen** (Target ~130% for consistent ultimates).\n    3.  **ATK%** & **Heavy Attack DMG Bonus**.\n\n---\n\n### III. The \"Perfect Rotation\"\nTo maximize Galbrena\'s damage, follow this sequence:\n\n1.  **Support Setup:** Switch to your supports/sub-DPS. Use their **Echo Skills** first to build Galbrena\'s **Afterflame** stacks.\n2.  **Sinflame Building:** Switch to Galbrena. Use Basic Attacks and her Resonance Skill until her Sinflame bar is full (100).\n3.  **The Burst Window:** * Cast **Resonance Liberation (R)** to activate her ultimate buffs.\n    * Immediately use the enhanced Resonance Skill (**Ascent of Malice**) to enter **Demon Hypostasis**.\n4.  **Demon Form Combo:** Spam her empowered Basic and Heavy attacks. Use **Hellstride** (dodge) to stay aggressive without breaking your chain.\n5.  **Exit:** Once her Forte energy is depleted, use her Outro Skill to swap back to supports and repeat.\n\n---\n\n### IV. Recommended Team Comps\n* **The Premium Hypercarry:** Galbrena + Qiuyuan + Shorekeeper. (Qiuyuan provides the best synergy for Galbrena\'s Echo-heavy playstyle).\n* **The Mono-Fusion Team:** Galbrena + Lupa + Mortefi/Verina. (Focused on shredding Fusion resistance and maximizing fire damage).\n\n> **Pro Tip:** Don\'t forget that Galbrena\'s 4th Basic Attack and 3rd Heavy Attack hit count as **Echo Skill DMG**. This is why the Corrosaurus and her signature weapon are so vital—they buff these specific \"hidden\" damage types!', NULL, NULL, '2026-04-14 17:20:39'),
(5, 5, 'Site', 'text', 'https://www.youtube.com/watch?v=7tFW4r7XYxk', 'https://game8.co/games/Wuthering-Waves/archives/524888', NULL, NULL, '2026-04-14 17:21:05'),
(6, 6, 'Trailer - Must watch', 'video', 'https://www.youtube.com/watch?v=optr9r9VkoQ', 'https://game8.co/games/Wuthering-Waves/archives/524888', NULL, NULL, '2026-04-14 17:22:30'),
(7, 7, 'Build', 'video', 'https://www.youtube.com/watch?v=6vkbhvQQC2I&t=619s', 'https://game8.co/games/Wuthering-Waves/archives/524888', NULL, NULL, '2026-04-14 17:23:17'),
(8, 7, 'Gameplay', 'video', 'https://www.youtube.com/watch?v=u6_i_o_llSU', 'https://game8.co/games/Wuthering-Waves/archives/524888', NULL, NULL, '2026-04-14 17:23:47'),
(9, 8, '## Course Lesson: Optimized Catethieya Build (Aero Main DPS)', 'text', 'https://www.youtube.com/watch?v=u6_i_o_llSU', 'Catethieya is a unique 5-star Aero sword-wielder with a dual-form mechanic. Her kit scales primarily off **HP** rather than ATK, making her gearing process distinct from traditional damage dealers. She alternates between her small form (Catethieya) for setup and her adult form (**Fleurdelys**) for massive burst damage.\n\n---\n\n### I. Core Mechanics: The Two Faces of Storm\nThe key to mastering Catethieya is managing her **Sword Shadows** and **Aero Erosion** stacks.\n\n* **Dual Forms:**\n    * **Catethieya (Small):** Focuses on mobility and speed. Her Basic/Heavy attacks summon **Sword Shadows** (Divinity, Discord, and Virtue). Using a **Plunging Attack** recalls these shadows for a massive damage spike.\n    * **Fleurdelys (Adult):** Activated via **Resonance Liberation**. In this state, she gains a **60% Aero DMG Bonus** and uses a high-impact 5-hit combo.\n* **Aero Erosion:** This is a stacking debuff applied to enemies. Catethieya deals significantly more damage to enemies based on how many stacks they have.\n\n---\n\n### II. Best Gear & Stats\n\n#### 1. Weapons\n* **Best in Slot:** *Defier’s Thorn* (Signature 5-star). This is non-negotiable for top-tier performance as it provides **72.2% HP** and DEF ignore.\n* **Premium Alternative:** *Emerald of Genesis* (Standard 5-star). Good for Energy Regen and Crit Rate, though you lose the HP scaling.\n* **F2P / Budget:** *Guardian Sword* (3-star). Surprisingly viable because of its **30% HP** sub-stat and Resonance Skill damage bonus at R5.\n\n#### 2. Echoes\n* **Best Set:** **Windward Pilgrimage** (5-piece). \n    * *Bonus:* +10% Aero DMG. After hitting with Aero Erosion, you gain +10% Crit Rate and +30% Aero DMG.\n* **Main Echo (Cost 4):** **Reminiscence: Fleurdelys**.\n    * *Why:* It provides a massive Aero DMG bonus specifically for the Rover (Aero) or Catethieya.\n* **Stat Priority:** * **4-Cost:** Crit Rate / Crit DMG.\n    * **3-Cost:** Aero DMG% / Aero DMG%.\n    * **1-Cost:** HP% / HP%.\n    * **Sub-stats:** Crit Rate > Crit DMG > **HP%** > Energy Regen (~120%).\n\n---\n\n### III. The Battle Rotation\nMaximize your damage by setting up the field before transforming:\n\n1.  **Preparation:** Use supports to group enemies and apply initial buffs.\n2.  **Shadow Summoning:** Use Catethieya’s Basic Attack (Stage 4) and Resonance Skill to summon all three **Sword Shadows**.\n3.  **The Recall:** Perform a **Plunging Attack** to recall the shadows and trigger the \"Aero Erosion\" burst.\n4.  **Transformation:** Once your energy is full, cast **Resonance Liberation** to enter the Fleurdelys form.\n5.  **Execution:** Spam the empowered ground and aerial combos. If your **Conviction** (meter) is full, use the finisher **Blade of Howling Squall** to clear the erosion stacks for maximum multiplier damage.\n\n---\n\n### IV. Recommended Team Comps\n* **The Aero Synergy:** Catethieya + **Ciaccona** + **Aero Rover**. (Ciaccona easily applies Aero Erosion, while Aero Rover acts as a healer/support who gets specific buffs when paired with Catethieya).\n* **Standard Hypercarry:** Catethieya + Shorekeeper + Verina. (Double support to keep her HP high and Crit stats boosted).\n\n> **Pro Tip:** Unlike most DPS units, Catethieya actually wants a **44111** Echo configuration (two 4-cost Echoes) if you are struggling with Crit stats, as her HP scaling is so high that you can afford to skip one 3-cost Aero slot if needed.', NULL, NULL, '2026-04-14 17:25:05'),
(10, 9, 'Intro', 'video', 'https://www.youtube.com/watch?v=PeMlggyqz0Y&pp=ygUabWFjaGluZSBsZWFybmluZyBleHBsYWluZWQ%3D', 'Catethieya is a unique 5-star Aero sword-wielder with a dual-form mechanic. Her kit scales primarily off **HP** rather than ATK, making her gearing process distinct from traditional damage dealers. She alternates between her small form (Catethieya) for setup and her adult form (**Fleurdelys**) for massive burst damage.\n\n---\n\n### I. Core Mechanics: The Two Faces of Storm\nThe key to mastering Catethieya is managing her **Sword Shadows** and **Aero Erosion** stacks.\n\n* **Dual Forms:**\n    * **Catethieya (Small):** Focuses on mobility and speed. Her Basic/Heavy attacks summon **Sword Shadows** (Divinity, Discord, and Virtue). Using a **Plunging Attack** recalls these shadows for a massive damage spike.\n    * **Fleurdelys (Adult):** Activated via **Resonance Liberation**. In this state, she gains a **60% Aero DMG Bonus** and uses a high-impact 5-hit combo.\n* **Aero Erosion:** This is a stacking debuff applied to enemies. Catethieya deals significantly more damage to enemies based on how many stacks they have.\n\n---\n\n### II. Best Gear & Stats\n\n#### 1. Weapons\n* **Best in Slot:** *Defier’s Thorn* (Signature 5-star). This is non-negotiable for top-tier performance as it provides **72.2% HP** and DEF ignore.\n* **Premium Alternative:** *Emerald of Genesis* (Standard 5-star). Good for Energy Regen and Crit Rate, though you lose the HP scaling.\n* **F2P / Budget:** *Guardian Sword* (3-star). Surprisingly viable because of its **30% HP** sub-stat and Resonance Skill damage bonus at R5.\n\n#### 2. Echoes\n* **Best Set:** **Windward Pilgrimage** (5-piece). \n    * *Bonus:* +10% Aero DMG. After hitting with Aero Erosion, you gain +10% Crit Rate and +30% Aero DMG.\n* **Main Echo (Cost 4):** **Reminiscence: Fleurdelys**.\n    * *Why:* It provides a massive Aero DMG bonus specifically for the Rover (Aero) or Catethieya.\n* **Stat Priority:** * **4-Cost:** Crit Rate / Crit DMG.\n    * **3-Cost:** Aero DMG% / Aero DMG%.\n    * **1-Cost:** HP% / HP%.\n    * **Sub-stats:** Crit Rate > Crit DMG > **HP%** > Energy Regen (~120%).\n\n---\n\n### III. The Battle Rotation\nMaximize your damage by setting up the field before transforming:\n\n1.  **Preparation:** Use supports to group enemies and apply initial buffs.\n2.  **Shadow Summoning:** Use Catethieya’s Basic Attack (Stage 4) and Resonance Skill to summon all three **Sword Shadows**.\n3.  **The Recall:** Perform a **Plunging Attack** to recall the shadows and trigger the \"Aero Erosion\" burst.\n4.  **Transformation:** Once your energy is full, cast **Resonance Liberation** to enter the Fleurdelys form.\n5.  **Execution:** Spam the empowered ground and aerial combos. If your **Conviction** (meter) is full, use the finisher **Blade of Howling Squall** to clear the erosion stacks for maximum multiplier damage.\n\n---\n\n### IV. Recommended Team Comps\n* **The Aero Synergy:** Catethieya + **Ciaccona** + **Aero Rover**. (Ciaccona easily applies Aero Erosion, while Aero Rover acts as a healer/support who gets specific buffs when paired with Catethieya).\n* **Standard Hypercarry:** Catethieya + Shorekeeper + Verina. (Double support to keep her HP high and Crit stats boosted).\n\n> **Pro Tip:** Unlike most DPS units, Catethieya actually wants a **44111** Echo configuration (two 4-cost Echoes) if you are struggling with Crit stats, as her HP scaling is so high that you can afford to skip one 3-cost Aero slot if needed.', NULL, NULL, '2026-04-14 17:25:49'),
(11, 10, 'course content', 'video', 'https://www.youtube.com/watch?v=i_LwzRVP7bg&pp=ygUcbWFjaGluZSBsZWFybmluZyBmdWxsIGNvdXJzZQ%3D%3D', 'Catethieya is a unique 5-star Aero sword-wielder with a dual-form mechanic. Her kit scales primarily off **HP** rather than ATK, making her gearing process distinct from traditional damage dealers. She alternates between her small form (Catethieya) for setup and her adult form (**Fleurdelys**) for massive burst damage.\n\n---\n\n### I. Core Mechanics: The Two Faces of Storm\nThe key to mastering Catethieya is managing her **Sword Shadows** and **Aero Erosion** stacks.\n\n* **Dual Forms:**\n    * **Catethieya (Small):** Focuses on mobility and speed. Her Basic/Heavy attacks summon **Sword Shadows** (Divinity, Discord, and Virtue). Using a **Plunging Attack** recalls these shadows for a massive damage spike.\n    * **Fleurdelys (Adult):** Activated via **Resonance Liberation**. In this state, she gains a **60% Aero DMG Bonus** and uses a high-impact 5-hit combo.\n* **Aero Erosion:** This is a stacking debuff applied to enemies. Catethieya deals significantly more damage to enemies based on how many stacks they have.\n\n---\n\n### II. Best Gear & Stats\n\n#### 1. Weapons\n* **Best in Slot:** *Defier’s Thorn* (Signature 5-star). This is non-negotiable for top-tier performance as it provides **72.2% HP** and DEF ignore.\n* **Premium Alternative:** *Emerald of Genesis* (Standard 5-star). Good for Energy Regen and Crit Rate, though you lose the HP scaling.\n* **F2P / Budget:** *Guardian Sword* (3-star). Surprisingly viable because of its **30% HP** sub-stat and Resonance Skill damage bonus at R5.\n\n#### 2. Echoes\n* **Best Set:** **Windward Pilgrimage** (5-piece). \n    * *Bonus:* +10% Aero DMG. After hitting with Aero Erosion, you gain +10% Crit Rate and +30% Aero DMG.\n* **Main Echo (Cost 4):** **Reminiscence: Fleurdelys**.\n    * *Why:* It provides a massive Aero DMG bonus specifically for the Rover (Aero) or Catethieya.\n* **Stat Priority:** * **4-Cost:** Crit Rate / Crit DMG.\n    * **3-Cost:** Aero DMG% / Aero DMG%.\n    * **1-Cost:** HP% / HP%.\n    * **Sub-stats:** Crit Rate > Crit DMG > **HP%** > Energy Regen (~120%).\n\n---\n\n### III. The Battle Rotation\nMaximize your damage by setting up the field before transforming:\n\n1.  **Preparation:** Use supports to group enemies and apply initial buffs.\n2.  **Shadow Summoning:** Use Catethieya’s Basic Attack (Stage 4) and Resonance Skill to summon all three **Sword Shadows**.\n3.  **The Recall:** Perform a **Plunging Attack** to recall the shadows and trigger the \"Aero Erosion\" burst.\n4.  **Transformation:** Once your energy is full, cast **Resonance Liberation** to enter the Fleurdelys form.\n5.  **Execution:** Spam the empowered ground and aerial combos. If your **Conviction** (meter) is full, use the finisher **Blade of Howling Squall** to clear the erosion stacks for maximum multiplier damage.\n\n---\n\n### IV. Recommended Team Comps\n* **The Aero Synergy:** Catethieya + **Ciaccona** + **Aero Rover**. (Ciaccona easily applies Aero Erosion, while Aero Rover acts as a healer/support who gets specific buffs when paired with Catethieya).\n* **Standard Hypercarry:** Catethieya + Shorekeeper + Verina. (Double support to keep her HP high and Crit stats boosted).\n\n> **Pro Tip:** Unlike most DPS units, Catethieya actually wants a **44111** Echo configuration (two 4-cost Echoes) if you are struggling with Crit stats, as her HP scaling is so high that you can afford to skip one 3-cost Aero slot if needed.', NULL, NULL, '2026-04-14 17:26:43'),
(12, 12, 'PP and AE', 'video', 'https://www.youtube.com/watch?v=GnZFNE4pz5I&pp=ygUKbWFrZSBtZW1lcw%3D%3D', 'Catethieya is a unique 5-star Aero sword-wielder with a dual-form mechanic. Her kit scales primarily off **HP** rather than ATK, making her gearing process distinct from traditional damage dealers. She alternates between her small form (Catethieya) for setup and her adult form (**Fleurdelys**) for massive burst damage.\n\n---\n\n### I. Core Mechanics: The Two Faces of Storm\nThe key to mastering Catethieya is managing her **Sword Shadows** and **Aero Erosion** stacks.\n\n* **Dual Forms:**\n    * **Catethieya (Small):** Focuses on mobility and speed. Her Basic/Heavy attacks summon **Sword Shadows** (Divinity, Discord, and Virtue). Using a **Plunging Attack** recalls these shadows for a massive damage spike.\n    * **Fleurdelys (Adult):** Activated via **Resonance Liberation**. In this state, she gains a **60% Aero DMG Bonus** and uses a high-impact 5-hit combo.\n* **Aero Erosion:** This is a stacking debuff applied to enemies. Catethieya deals significantly more damage to enemies based on how many stacks they have.\n\n---\n\n### II. Best Gear & Stats\n\n#### 1. Weapons\n* **Best in Slot:** *Defier’s Thorn* (Signature 5-star). This is non-negotiable for top-tier performance as it provides **72.2% HP** and DEF ignore.\n* **Premium Alternative:** *Emerald of Genesis* (Standard 5-star). Good for Energy Regen and Crit Rate, though you lose the HP scaling.\n* **F2P / Budget:** *Guardian Sword* (3-star). Surprisingly viable because of its **30% HP** sub-stat and Resonance Skill damage bonus at R5.\n\n#### 2. Echoes\n* **Best Set:** **Windward Pilgrimage** (5-piece). \n    * *Bonus:* +10% Aero DMG. After hitting with Aero Erosion, you gain +10% Crit Rate and +30% Aero DMG.\n* **Main Echo (Cost 4):** **Reminiscence: Fleurdelys**.\n    * *Why:* It provides a massive Aero DMG bonus specifically for the Rover (Aero) or Catethieya.\n* **Stat Priority:** * **4-Cost:** Crit Rate / Crit DMG.\n    * **3-Cost:** Aero DMG% / Aero DMG%.\n    * **1-Cost:** HP% / HP%.\n    * **Sub-stats:** Crit Rate > Crit DMG > **HP%** > Energy Regen (~120%).\n\n---\n\n### III. The Battle Rotation\nMaximize your damage by setting up the field before transforming:\n\n1.  **Preparation:** Use supports to group enemies and apply initial buffs.\n2.  **Shadow Summoning:** Use Catethieya’s Basic Attack (Stage 4) and Resonance Skill to summon all three **Sword Shadows**.\n3.  **The Recall:** Perform a **Plunging Attack** to recall the shadows and trigger the \"Aero Erosion\" burst.\n4.  **Transformation:** Once your energy is full, cast **Resonance Liberation** to enter the Fleurdelys form.\n5.  **Execution:** Spam the empowered ground and aerial combos. If your **Conviction** (meter) is full, use the finisher **Blade of Howling Squall** to clear the erosion stacks for maximum multiplier damage.\n\n---\n\n### IV. Recommended Team Comps\n* **The Aero Synergy:** Catethieya + **Ciaccona** + **Aero Rover**. (Ciaccona easily applies Aero Erosion, while Aero Rover acts as a healer/support who gets specific buffs when paired with Catethieya).\n* **Standard Hypercarry:** Catethieya + Shorekeeper + Verina. (Double support to keep her HP high and Crit stats boosted).\n\n> **Pro Tip:** Unlike most DPS units, Catethieya actually wants a **44111** Echo configuration (two 4-cost Echoes) if you are struggling with Crit stats, as her HP scaling is so high that you can afford to skip one 3-cost Aero slot if needed.', NULL, NULL, '2026-04-14 17:28:27'),
(13, 11, 'Intro', 'video', 'https://www.youtube.com/watch?v=Aq5WXmQQooo', 'Catethieya is a unique 5-star Aero sword-wielder with a dual-form mechanic. Her kit scales primarily off **HP** rather than ATK, making her gearing process distinct from traditional damage dealers. She alternates between her small form (Catethieya) for setup and her adult form (**Fleurdelys**) for massive burst damage.\n\n---\n\n### I. Core Mechanics: The Two Faces of Storm\nThe key to mastering Catethieya is managing her **Sword Shadows** and **Aero Erosion** stacks.\n\n* **Dual Forms:**\n    * **Catethieya (Small):** Focuses on mobility and speed. Her Basic/Heavy attacks summon **Sword Shadows** (Divinity, Discord, and Virtue). Using a **Plunging Attack** recalls these shadows for a massive damage spike.\n    * **Fleurdelys (Adult):** Activated via **Resonance Liberation**. In this state, she gains a **60% Aero DMG Bonus** and uses a high-impact 5-hit combo.\n* **Aero Erosion:** This is a stacking debuff applied to enemies. Catethieya deals significantly more damage to enemies based on how many stacks they have.\n\n---\n\n### II. Best Gear & Stats\n\n#### 1. Weapons\n* **Best in Slot:** *Defier’s Thorn* (Signature 5-star). This is non-negotiable for top-tier performance as it provides **72.2% HP** and DEF ignore.\n* **Premium Alternative:** *Emerald of Genesis* (Standard 5-star). Good for Energy Regen and Crit Rate, though you lose the HP scaling.\n* **F2P / Budget:** *Guardian Sword* (3-star). Surprisingly viable because of its **30% HP** sub-stat and Resonance Skill damage bonus at R5.\n\n#### 2. Echoes\n* **Best Set:** **Windward Pilgrimage** (5-piece). \n    * *Bonus:* +10% Aero DMG. After hitting with Aero Erosion, you gain +10% Crit Rate and +30% Aero DMG.\n* **Main Echo (Cost 4):** **Reminiscence: Fleurdelys**.\n    * *Why:* It provides a massive Aero DMG bonus specifically for the Rover (Aero) or Catethieya.\n* **Stat Priority:** * **4-Cost:** Crit Rate / Crit DMG.\n    * **3-Cost:** Aero DMG% / Aero DMG%.\n    * **1-Cost:** HP% / HP%.\n    * **Sub-stats:** Crit Rate > Crit DMG > **HP%** > Energy Regen (~120%).\n\n---\n\n### III. The Battle Rotation\nMaximize your damage by setting up the field before transforming:\n\n1.  **Preparation:** Use supports to group enemies and apply initial buffs.\n2.  **Shadow Summoning:** Use Catethieya’s Basic Attack (Stage 4) and Resonance Skill to summon all three **Sword Shadows**.\n3.  **The Recall:** Perform a **Plunging Attack** to recall the shadows and trigger the \"Aero Erosion\" burst.\n4.  **Transformation:** Once your energy is full, cast **Resonance Liberation** to enter the Fleurdelys form.\n5.  **Execution:** Spam the empowered ground and aerial combos. If your **Conviction** (meter) is full, use the finisher **Blade of Howling Squall** to clear the erosion stacks for maximum multiplier damage.\n\n---\n\n### IV. Recommended Team Comps\n* **The Aero Synergy:** Catethieya + **Ciaccona** + **Aero Rover**. (Ciaccona easily applies Aero Erosion, while Aero Rover acts as a healer/support who gets specific buffs when paired with Catethieya).\n* **Standard Hypercarry:** Catethieya + Shorekeeper + Verina. (Double support to keep her HP high and Crit stats boosted).\n\n> **Pro Tip:** Unlike most DPS units, Catethieya actually wants a **44111** Echo configuration (two 4-cost Echoes) if you are struggling with Crit stats, as her HP scaling is so high that you can afford to skip one 3-cost Aero slot if needed.', NULL, NULL, '2026-04-14 17:29:00'),
(14, 13, '## Course Lesson: Introduction to Machine Learning (2026 Edition)', 'text', 'https://www.youtube.com/watch?v=Aq5WXmQQooo', '\n\nMachine Learning (ML) is the field of Artificial Intelligence that focuses on building systems that **learn from data** to make decisions or predictions, rather than following explicitly programmed rules. In 2026, ML has moved beyond simple \"prediction\" into \"agentic\" systems that can take actions autonomously.\n\n---\n\n### I. The Three Pillars of Machine Learning\nMost ML tasks fall into one of these three categories:\n\n1.  **Supervised Learning (Learning with a Label):** * The model is trained on \"input-output\" pairs. It knows the \"correct answer\" for the training data.\n    * *Examples:* Predicting house prices (Regression) or identifying if an email is spam (Classification).\n2.  **Unsupervised Learning (Finding Hidden Patterns):** * The model looks at unlabeled data and tries to find structure on its own.\n    * *Examples:* Grouping customers by shopping habits (Clustering) or identifying fraudulent credit card swipes (Anomaly Detection).\n3.  **Reinforcement Learning (Learning by Trial and Error):** * An \"agent\" learns to achieve a goal in an environment by receiving rewards or penalties.\n    * *Examples:* Robots learning to walk or AI players in video games.\n\n---\n\n### II. The Modern ML Workflow (MLOps)\nIn today\'s industry, we don\'t just \"train a model.\" We follow a lifecycle called **MLOps** (Machine Learning Operations):\n\n* **Data Preparation:** Cleaning raw data and \"Feature Engineering\" (selecting the most important variables).\n* **Training & Fine-tuning:** Using algorithms like **Gradient Descent** to minimize errors.\n* **Evaluation:** Checking for **Overfitting** (when a model memorizes data instead of learning) and **Bias**.\n* **Deployment:** Putting the model into an app. In 2026, this often happens at the **Edge** (directly on your phone or IoT device) to save power and improve privacy.\n\n---\n\n### III. 2026 Specialized Concepts\nMachine Learning has evolved. Here are the three most relevant \"modern\" branches:\n\n* **Deep Learning:** Uses \"Neural Networks\" inspired by the human brain. This powers almost all modern Computer Vision and Natural Language Processing.\n* **Generative AI & LLMs:** Large models (like those using the **Transformer** architecture) that generate new content rather than just classifying existing data.\n* **Agentic AI:** The newest trend where ML models use tools (search, calculators, code interpreters) to complete complex, multi-step tasks without human intervention.\n\n---\n\n### IV. Summary Checklist\n* **Objective:** To find patterns in data without being told exactly what to do.\n* **Core Requirements:** High-quality data, computing power (GPUs), and a clear evaluation metric.\n* **Ethical Priority:** In 2026, **Explainable AI (XAI)** is critical—we must be able to explain *why* a model made a specific decision to ensure fairness and transparency.\n\n---\n\n**Quick Knowledge Check:** If you wanted to build a system that sorts photos of fruit into \"Apples\" and \"Oranges,\" and you already have thousands of labeled photos to show it, which type of learning would you use? \n*(Answer: Supervised Learning)*', NULL, NULL, '2026-04-14 17:29:48'),
(15, 14, '## Course Lesson: The Art of Making Memes (Memeez)', 'text', 'https://www.youtube.com/watch?v=Aq5WXmQQooo', '\n\nMemes are the core language of the internet. A great meme is more than just a funny picture; it\'s a rapidly shareable, highly relatable cultural artifact that mixes visuals with specific context (or profound lack thereof). In 2026, the meme landscape has shifted from static templates to interactive, AI-enhanced, and hyper-niche content.\n\n---\n\n### I. The Anatomy of a Modern Meme\n\nA successful modern meme generally requires three components, though not all must be present:\n\n* **The Visual Hook (Template or Format):** This is the underlying image, video clip, GIF, or short animation. Classic examples include the \"Distracted Boyfriend\" or \"Woman Yelling at a Cat.\" In 2026, many visuals are sourced from trending shows, obscure video games, or are **AI-generated** from scratch.\n* **The Text/Context (Caption):** This is where you apply the relatable situation. It must be snappy, clever, and match the emotional tone of the visual. *Self-deprecating humor and situational irony are always high-value.*\n* **The Shared Experience (Relatability):** A good meme connects people. It addresses a shared feeling, specific cultural moment, universal inconvenience, or hyper-niche fandom experience.\n\n---\n\n### II. The Creation Workflow (Step-by-Step)\n\n#### 1. Identify the Core Idea (The \"Why\")\nAsk yourself: *What is the message, feeling, or observation I want to convey?*\n* Is it about student loan debt?\n* Is it about waiting for a new update to drop?\n* Is it just complete nonsense (the \"shitposting\" philosophy)?\n\n#### 2. Choose Your Format\n* **Static Image/Image Macro:** The classic visual. (e.g., Image of a character + bold text).\n* **GIF/Video Meme:** Using short clips (often with audio) as the vehicle for the joke.\n* **Interactive/Augmented Reality (AR) Meme:** The newest trend. These are memes applied as dynamic filters (e.g., a filter that makes you look like a specific crying character when you talk about your bills).\n\n#### 3. Execution (The Assembly)\n* **Static:** Use tools like Canva, Photoshop, or basic meme generators. The \"Impact\" font (white text with black border) is the classic, but modern memes use standard, cleaner fonts.\n* **Video/GIF:** Use apps like CapCut, Adobe Premiere Rush, or native tools on social apps (TikTok, Instagram Reels). Overlay text at key points to land the punchline.\n\n---\n\n### III. 2026 Special: AI Meme Creation\n\nThe single biggest change to meme-making is Generative AI. This is where \"Memeez\" truly come from now.\n\n* **Text-to-Image Generation:** You don\'t need to find the perfect photo; you can generate it.\n    * *Prompt Idea:* \"A realistic portrait of a cat wearing a corporate suit, looking stressed, while drinking a miniature cup of coffee, in a chaotic office environment.\" (Use this as your base image).\n* **Text-to-Video Generation:** Create short, weird video clips that serve as new templates.\n* **Real-time Synthesis:** Live-translating dialogue in video clips or swapping faces onto standard templates.\n\n---\n\n### IV. Summary Checklist for Virality\n\n* **Context is King:** Understand who your audience is. A meme that is *too* broad is bland. A meme that is *too* niche might only get 10 likes, but they will be 10 *quality* likes.\n* **Simplicity Wins:** A punchline that requires a paragraph to understand is not a meme. Keep it brief.\n* **Timing is Key:** Hop on a trend *immediately*. Meme templates have a incredibly short lifespan (sometimes only a few hours).\n* **Don\'t Force It:** The internet smells desperation. Authentically funny or bizarre content performs better than corporate attempts to \"be relatable.\"\n\n---\n\n**Practical Exercise:** Pick a minor daily annoyance (e.g., running out of coffee, your laptop dying right before a deadline). Now, generate a visual using a prompt like \"a dramatic, classical oil painting showing immense grief and despair\" and apply your annoyance as the caption. You have just made a 2026 \"Memeez.\"', NULL, NULL, '2026-04-14 17:30:20'),
(16, 15, 'what\'s here', 'video', 'https://www.youtube.com/watch?v=1Z1MRddQKSM', 'https://www.youtube.com/watch?v=1Z1MRddQKSM', NULL, NULL, '2026-04-14 17:31:42'),
(17, 16, 'Recipe that will make people mad frfr', 'video', 'https://www.youtube.com/watch?v=EYXQmbZNhy8&t=236s', 'https://www.youtube.com/watch?v=1Z1MRddQKSM', NULL, NULL, '2026-04-14 17:32:45'),
(18, 17, '## Course Lesson: Launching a Cake Business in Agargaon', 'text', 'https://www.youtube.com/watch?v=EYXQmbZNhy8&t=236s', '\nAgargaon is a unique hub in Dhaka, blending government offices, residential pockets like Taltola and Monipuripara, and the busy tech market area. Starting a cake business here—whether it\'s a physical shop or a home-based kitchen—requires a mix of local visibility and digital savvy.\n\n---\n\n### I. Market Research: The Agargaon Landscape\n\nTo succeed in Agargaon, you need to understand your three primary customer segments:\n\n1.  **Office Professionals:** Government employees (Election Commission, IDB Bhaban, various Ministries) who need quick snacks, \"work anniversary\" cakes, or retirement celebration treats.\n2.  **Students & Families:** Residents in nearby Taltola, Shewrapara, and residential colonies looking for birthday and celebration cakes.\n3.  **The \"Viral\" Crowd:** Agargaon is becoming a \"street food\" hotspot. Recent trends like the \"Jakir Bhai Viral Cake\" (famous for affordable, quick-buy slices) show that high-volume, low-cost options can explode in popularity here.\n\n---\n\n### II. Choosing Your Business Model\n\n| Feature | **Home-Based (Online)** | **Commercial Shop/Bakery** |\n| :--- | :--- | :--- |\n| **Startup Cost** | Low (Kitchen equipment + Social media). | High (Rent, Interior, Trade license, Staff). |\n| **Focus** | Customized, premium, and artisanal cakes. | High-volume sales, pastries, and breads. |\n| **Visibility** | Relies on Facebook/Instagram/Pathao Food. | Foot traffic from Agargaon main roads/markets. |\n| **Example** | *Cakes By Chef* (Online model). | *Bread Basket* or *Mr. Baker*. |\n\n---\n\n### III. Menu & Pricing Strategy (Local Context)\n\nIn a market like Dhaka, your menu should cater to both the \"Sweet Tooth\" and the \"Gift Giver.\"\n\n* **The Signature Items:** Offer flavors that perform well in Bangladesh, such as **Red Velvet with Cream Cheese**, **Belgian Chocolate**, and **Vanilla with Fruit Filling**.\n* **The \"Pocket-Friendly\" Tier:** Small jars, cupcakes, or \"bento cakes\" (mini cakes) priced between **৳250 – ৳600** for students and casual buyers.\n* **The Premium Tier:** Tiered wedding or birthday cakes priced by weight (e.g., **৳1,200 – ৳2,500 per kg** depending on decoration).\n* **Customization:** Since Agargaon is a tech and office hub, offer \"Tech-themed\" cakes (e.g., Laptop or Code-themed cakes for IDB workers) or \"Official/Formal\" designs.\n\n---\n\n### IV. Marketing & Delivery in Agargaon\n\n1.  **Social Media Presence:** Focus on high-quality photos and videos (Reels/TikTok). In Dhaka, **Facebook Groups** (like Deshi Foodies) are powerful for initial traction.\n2.  **Hyper-Local SEO:** Register your business on **Google Maps**. When people in Agargaon search for \"cakes near me,\" you want your kitchen/shop to pop up first.\n3.  **Delivery Logistics:** Agargaon can have tricky traffic during office hours. \n    * Partner with **Pathao Food** and **Foodpanda** for wider reach.\n    * Offer \"Local Pickup\" points near well-known landmarks like the **National Archives** or **IDB Bhaban** to save on delivery costs for customers.\n\n---\n\n### V. Legal & Hygiene Checklist\n\n* **Trade License:** Essential if you open a physical shop; recommended for online businesses as they scale.\n* **BSTI Certification:** Necessary if you plan to sell packaged baked goods in retail stores.\n* **Packaging:** Invest in sturdy, branded boxes. In the humid Dhaka weather, moisture-resistant packaging is a must to keep the frosting intact.\n\n---\n\n**Summary:** Whether you aim to be the next viral street-side cake sensation or a high-end customized baker, the key in Agargaon is **consistency** and **local presence**. \n\nWould you like the next lesson to focus on the **cost breakdown (budgeting)** for a home-bakery or **digital marketing strategies** for Dhaka-based food businesses?', NULL, NULL, '2026-04-14 17:33:42'),
(19, 18, '100s of pytorch', 'video', 'https://www.youtube.com/watch?v=ORMx45xqWkA', '\nAgargaon is a unique hub in Dhaka, blending government offices, residential pockets like Taltola and Monipuripara, and the busy tech market area. Starting a cake business here—whether it\'s a physical shop or a home-based kitchen—requires a mix of local visibility and digital savvy.\n\n---\n\n### I. Market Research: The Agargaon Landscape\n\nTo succeed in Agargaon, you need to understand your three primary customer segments:\n\n1.  **Office Professionals:** Government employees (Election Commission, IDB Bhaban, various Ministries) who need quick snacks, \"work anniversary\" cakes, or retirement celebration treats.\n2.  **Students & Families:** Residents in nearby Taltola, Shewrapara, and residential colonies looking for birthday and celebration cakes.\n3.  **The \"Viral\" Crowd:** Agargaon is becoming a \"street food\" hotspot. Recent trends like the \"Jakir Bhai Viral Cake\" (famous for affordable, quick-buy slices) show that high-volume, low-cost options can explode in popularity here.\n\n---\n\n### II. Choosing Your Business Model\n\n| Feature | **Home-Based (Online)** | **Commercial Shop/Bakery** |\n| :--- | :--- | :--- |\n| **Startup Cost** | Low (Kitchen equipment + Social media). | High (Rent, Interior, Trade license, Staff). |\n| **Focus** | Customized, premium, and artisanal cakes. | High-volume sales, pastries, and breads. |\n| **Visibility** | Relies on Facebook/Instagram/Pathao Food. | Foot traffic from Agargaon main roads/markets. |\n| **Example** | *Cakes By Chef* (Online model). | *Bread Basket* or *Mr. Baker*. |\n\n---\n\n### III. Menu & Pricing Strategy (Local Context)\n\nIn a market like Dhaka, your menu should cater to both the \"Sweet Tooth\" and the \"Gift Giver.\"\n\n* **The Signature Items:** Offer flavors that perform well in Bangladesh, such as **Red Velvet with Cream Cheese**, **Belgian Chocolate**, and **Vanilla with Fruit Filling**.\n* **The \"Pocket-Friendly\" Tier:** Small jars, cupcakes, or \"bento cakes\" (mini cakes) priced between **৳250 – ৳600** for students and casual buyers.\n* **The Premium Tier:** Tiered wedding or birthday cakes priced by weight (e.g., **৳1,200 – ৳2,500 per kg** depending on decoration).\n* **Customization:** Since Agargaon is a tech and office hub, offer \"Tech-themed\" cakes (e.g., Laptop or Code-themed cakes for IDB workers) or \"Official/Formal\" designs.\n\n---\n\n### IV. Marketing & Delivery in Agargaon\n\n1.  **Social Media Presence:** Focus on high-quality photos and videos (Reels/TikTok). In Dhaka, **Facebook Groups** (like Deshi Foodies) are powerful for initial traction.\n2.  **Hyper-Local SEO:** Register your business on **Google Maps**. When people in Agargaon search for \"cakes near me,\" you want your kitchen/shop to pop up first.\n3.  **Delivery Logistics:** Agargaon can have tricky traffic during office hours. \n    * Partner with **Pathao Food** and **Foodpanda** for wider reach.\n    * Offer \"Local Pickup\" points near well-known landmarks like the **National Archives** or **IDB Bhaban** to save on delivery costs for customers.\n\n---\n\n### V. Legal & Hygiene Checklist\n\n* **Trade License:** Essential if you open a physical shop; recommended for online businesses as they scale.\n* **BSTI Certification:** Necessary if you plan to sell packaged baked goods in retail stores.\n* **Packaging:** Invest in sturdy, branded boxes. In the humid Dhaka weather, moisture-resistant packaging is a must to keep the frosting intact.\n\n---\n\n**Summary:** Whether you aim to be the next viral street-side cake sensation or a high-end customized baker, the key in Agargaon is **consistency** and **local presence**. \n\nWould you like the next lesson to focus on the **cost breakdown (budgeting)** for a home-bakery or **digital marketing strategies** for Dhaka-based food businesses?', NULL, NULL, '2026-04-14 17:34:32'),
(20, 19, 'ALL in ONE', 'video', 'https://www.youtube.com/watch?v=V_xro1bcAuA', '\nAgargaon is a unique hub in Dhaka, blending government offices, residential pockets like Taltola and Monipuripara, and the busy tech market area. Starting a cake business here—whether it\'s a physical shop or a home-based kitchen—requires a mix of local visibility and digital savvy.\n\n---\n\n### I. Market Research: The Agargaon Landscape\n\nTo succeed in Agargaon, you need to understand your three primary customer segments:\n\n1.  **Office Professionals:** Government employees (Election Commission, IDB Bhaban, various Ministries) who need quick snacks, \"work anniversary\" cakes, or retirement celebration treats.\n2.  **Students & Families:** Residents in nearby Taltola, Shewrapara, and residential colonies looking for birthday and celebration cakes.\n3.  **The \"Viral\" Crowd:** Agargaon is becoming a \"street food\" hotspot. Recent trends like the \"Jakir Bhai Viral Cake\" (famous for affordable, quick-buy slices) show that high-volume, low-cost options can explode in popularity here.\n\n---\n\n### II. Choosing Your Business Model\n\n| Feature | **Home-Based (Online)** | **Commercial Shop/Bakery** |\n| :--- | :--- | :--- |\n| **Startup Cost** | Low (Kitchen equipment + Social media). | High (Rent, Interior, Trade license, Staff). |\n| **Focus** | Customized, premium, and artisanal cakes. | High-volume sales, pastries, and breads. |\n| **Visibility** | Relies on Facebook/Instagram/Pathao Food. | Foot traffic from Agargaon main roads/markets. |\n| **Example** | *Cakes By Chef* (Online model). | *Bread Basket* or *Mr. Baker*. |\n\n---\n\n### III. Menu & Pricing Strategy (Local Context)\n\nIn a market like Dhaka, your menu should cater to both the \"Sweet Tooth\" and the \"Gift Giver.\"\n\n* **The Signature Items:** Offer flavors that perform well in Bangladesh, such as **Red Velvet with Cream Cheese**, **Belgian Chocolate**, and **Vanilla with Fruit Filling**.\n* **The \"Pocket-Friendly\" Tier:** Small jars, cupcakes, or \"bento cakes\" (mini cakes) priced between **৳250 – ৳600** for students and casual buyers.\n* **The Premium Tier:** Tiered wedding or birthday cakes priced by weight (e.g., **৳1,200 – ৳2,500 per kg** depending on decoration).\n* **Customization:** Since Agargaon is a tech and office hub, offer \"Tech-themed\" cakes (e.g., Laptop or Code-themed cakes for IDB workers) or \"Official/Formal\" designs.\n\n---\n\n### IV. Marketing & Delivery in Agargaon\n\n1.  **Social Media Presence:** Focus on high-quality photos and videos (Reels/TikTok). In Dhaka, **Facebook Groups** (like Deshi Foodies) are powerful for initial traction.\n2.  **Hyper-Local SEO:** Register your business on **Google Maps**. When people in Agargaon search for \"cakes near me,\" you want your kitchen/shop to pop up first.\n3.  **Delivery Logistics:** Agargaon can have tricky traffic during office hours. \n    * Partner with **Pathao Food** and **Foodpanda** for wider reach.\n    * Offer \"Local Pickup\" points near well-known landmarks like the **National Archives** or **IDB Bhaban** to save on delivery costs for customers.\n\n---\n\n### V. Legal & Hygiene Checklist\n\n* **Trade License:** Essential if you open a physical shop; recommended for online businesses as they scale.\n* **BSTI Certification:** Necessary if you plan to sell packaged baked goods in retail stores.\n* **Packaging:** Invest in sturdy, branded boxes. In the humid Dhaka weather, moisture-resistant packaging is a must to keep the frosting intact.\n\n---\n\n**Summary:** Whether you aim to be the next viral street-side cake sensation or a high-end customized baker, the key in Agargaon is **consistency** and **local presence**. \n\nWould you like the next lesson to focus on the **cost breakdown (budgeting)** for a home-bakery or **digital marketing strategies** for Dhaka-based food businesses?', NULL, NULL, '2026-04-14 17:35:35'),
(21, 20, '## Course Lesson: Deep Learning with PyTorch', 'text', 'https://www.youtube.com/watch?v=V_xro1bcAuA', '\n\nPyTorch is a premier Python-based machine learning framework used by research labs and tech giants. Unlike other frameworks that use static graphs, PyTorch uses a **Dynamic Computational Graph**, meaning the network is built on-the-fly as code executes. This makes it incredibly intuitive and easy to debug.\n\n---\n\n### I. The Fundamental Unit: The Tensor\nAt its heart, PyTorch is a library for processing **Tensors**. Think of a tensor as a multi-dimensional array (like NumPy), but with two superpowers:\n1.  **GPU Acceleration:** Tensors can be moved to a GPU to perform math thousands of times faster than a CPU.\n2.  **Autograd:** They keep track of every operation performed on them to calculate gradients automatically.\n\n\n\n---\n\n### II. The \"Big Three\" Modules\nTo build any neural network in PyTorch, you will interact with these three main packages:\n\n* **`torch.nn`:** Contains the building blocks for neural networks (Layers, Activation functions, Loss functions).\n* **`torch.optim`:** Contains optimization algorithms like SGD (Stochastic Gradient Descent) or Adam that update your model\'s weights.\n* **`torch.autograd`:** The engine that powers the \"Backward Pass\" by calculating derivatives via the chain rule.\n\n---\n\n### III. The PyTorch Workflow (The 5-Step Loop)\nEvery training script follows this standard pattern:\n\n1.  **Define the Model:** Create a class that inherits from `nn.Module`.\n2.  **Forward Pass:** Pass your data through the model to get a prediction.\n3.  **Calculate Loss:** Compare the prediction to the actual target using a loss function (e.g., `MSELoss` for regression).\n4.  **Backward Pass:** Call `loss.backward()`. PyTorch travels back through your operations to find how much each weight contributed to the error.\n5.  **Update Weights:** Tell the optimizer to take a \"step\" (`optimizer.step()`) to slightly adjust the weights and reduce the error.\n\n\n\n---\n\n### IV. Code Anatomy: A Simple Model\nHere is what a basic PyTorch setup looks like in practice:\n\n```python\nimport torch\nimport torch.nn as nn\n\n# 1. Define Model\nclass SimpleNet(nn.Module):\n    def __init__(self):\n        super().__init__()\n        self.layer = nn.Linear(10, 1) # 10 inputs, 1 output\n\n    def forward(self, x):\n        return self.layer(x)\n\nmodel = SimpleNet()\noptimizer = torch.optim.SGD(model.parameters(), lr=0.01)\ncriterion = nn.MSELoss()\n\n# 2. Training Step (Simplified)\nprediction = model(torch.randn(1, 10))  # Forward\nloss = criterion(prediction, torch.tensor([1.0])) # Loss\noptimizer.zero_grad() # Clear old gradients\nloss.backward()       # Backward\noptimizer.step()      # Update\n```\n\n---\n\n### V. Why Use PyTorch in 2026?\n* **Research to Production:** With tools like `torch.compile`, models that are easy to write in Python can now be optimized for high-speed deployment without changing the code.\n* **Ecosystem:** Most modern Large Language Models (LLMs) and Generative AI research are released first in PyTorch.\n* **Readability:** It feels like native Python. If you can write a `for` loop, you can write a training loop.\n\n---\n\n**Quick Summary Checklist:**\n* **Tensors** are the data containers.\n* **`nn.Module`** is the blueprint for your network.\n* **`backward()`** is the magic that calculates how to improve.\n* **`optimizer`** is the tool that actually makes the changes.\n\nWould you like the next lesson to dive into **building your first Image Classifier** or an explanation of **how Autograd works** under the hood?', NULL, NULL, '2026-04-14 17:36:22'),
(24, 23, 'Trailer - Must watch', 'video', 'https://www.youtube.com/watch?v=-KPYMrQmFhA', '', NULL, NULL, '2026-04-14 20:53:09'),
(25, 24, 'Intro', 'text', 'https://www.youtube.com/watch?v=-KPYMrQmFhA', 'Arknights: Endfield guide: Master manufacturing and build a top team with these tips\n\n3.5.2026\nBy Diego Perez, Contributor\n\nArknights: Endfield may seem like other open-world gacha games at first glance, but several key mechanics set this action-RPG apart from the crowd.\n \nThere\'s a lot more to Arknights: Endfield than the flashy combat and cool characters, and your first day at Endfield Industries can be overwhelming. You\'ve got a whole factory to run, and your responsibilities will only grow as you add more outposts to your network. Keeping the operation running smoothly will keep your units in tip-top shape, but ignoring the logistics can cause you to fall behind.\n\nIt\'s a lot to take in, and while things will slowly start to click over the course of a few hours, we\'ve put together some tips to help you get started. Read on—our beginner\'s guide to Arknights: Endfield will help you find success on Talos-II.', NULL, NULL, '2026-04-14 20:53:41');

-- --------------------------------------------------------

--
-- Table structure for table `moderation_report`
--

CREATE TABLE `moderation_report` (
  `ReportID` int(11) NOT NULL,
  `ReportedUserID` int(11) DEFAULT NULL,
  `ReportedByUserID` int(11) DEFAULT NULL,
  `Reason` text NOT NULL,
  `ReportDate` datetime DEFAULT current_timestamp(),
  `ResolutionStatus` enum('pending','resolved','dismissed') DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `moderation_report`
--

INSERT INTO `moderation_report` (`ReportID`, `ReportedUserID`, `ReportedByUserID`, `Reason`, `ReportDate`, `ResolutionStatus`) VALUES
(1, 5, 2, 'Policy Violation: Sleeps in class', '2026-04-15 02:44:19', 'pending');

-- --------------------------------------------------------

--
-- Table structure for table `module`
--

CREATE TABLE `module` (
  `ModuleID` int(11) NOT NULL,
  `CourseID` int(11) DEFAULT NULL,
  `Title` varchar(255) NOT NULL,
  `Description` text DEFAULT NULL,
  `SequenceOrder` int(11) DEFAULT NULL,
  `CreatedAt` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `module`
--

INSERT INTO `module` (`ModuleID`, `CourseID`, `Title`, `Description`, `SequenceOrder`, `CreatedAt`) VALUES
(4, 3, 'Welcome', 'Into about her', NULL, '2026-04-14 17:18:37'),
(5, 3, 'Build guide', '', NULL, '2026-04-14 17:19:08'),
(6, 5, 'Weclome', 'intro', NULL, '2026-04-14 17:21:31'),
(7, 5, 'Training Tutorial', 'lesson clips', NULL, '2026-04-14 17:22:47'),
(8, 5, 'Build Notes', '', NULL, '2026-04-14 17:24:09'),
(9, 6, 'Intro', '', NULL, '2026-04-14 17:25:38'),
(10, 6, 'Lesson 1 - 5', '', NULL, '2026-04-14 17:26:25'),
(11, 7, 'HELLO pals!!@', '', NULL, '2026-04-14 17:27:20'),
(12, 7, 'Tutorial', '', NULL, '2026-04-14 17:28:11'),
(13, 6, 'Reading materials', '', NULL, '2026-04-14 17:29:34'),
(14, 7, 'Read it', '', NULL, '2026-04-14 17:30:07'),
(15, 8, 'THE GIG', '', NULL, '2026-04-14 17:31:15'),
(16, 8, 'CAKE making', '', NULL, '2026-04-14 17:31:57'),
(17, 8, 'Reading material', 'read it champ', NULL, '2026-04-14 17:33:11'),
(18, 9, 'Intro', '', NULL, '2026-04-14 17:33:55'),
(19, 9, 'all lesson', '', NULL, '2026-04-14 17:35:24'),
(20, 9, 'READING', '', NULL, '2026-04-14 17:36:05'),
(23, 11, 'Week 1', '', NULL, '2026-04-14 20:52:48'),
(24, 11, 'week 2', '', NULL, '2026-04-14 20:53:19');

-- --------------------------------------------------------

--
-- Table structure for table `notification`
--

CREATE TABLE `notification` (
  `NotificationID` int(11) NOT NULL,
  `UserID` int(11) NOT NULL,
  `Message` text NOT NULL,
  `IsRead` tinyint(1) DEFAULT 0,
  `CreatedAt` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `question`
--

CREATE TABLE `question` (
  `QuestionID` int(11) NOT NULL,
  `AssessmentID` int(11) DEFAULT NULL,
  `QuestionText` text NOT NULL,
  `OptionA` varchar(255) DEFAULT NULL,
  `OptionB` varchar(255) DEFAULT NULL,
  `OptionC` varchar(255) DEFAULT NULL,
  `OptionD` varchar(255) DEFAULT NULL,
  `CorrectOptionIndex` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `question`
--

INSERT INTO `question` (`QuestionID`, `AssessmentID`, `QuestionText`, `OptionA`, `OptionB`, `OptionC`, `OptionD`, `CorrectOptionIndex`) VALUES
(4, 10, 'Which resource must Galbrena reach 100 in order to transform into her &#039;Demon Hypostasis&#039; form via her Resonance Skill?', 'Afterflame', 'Sinflame', 'Concerto Energy', 'Resonance Energy', 2),
(5, 10, 'Why is the &#039;Corrosaurus&#039; Echo specifically recommended for her?', 't provides a shield based on DEF.', 'It increases Glacio damage.', 'It buffs Fusion and Echo Skill damage.', 't reduces stamina consumption.', 2),
(6, 11, 'What is the primary stat Catethieya uses for her damage scaling?', 'ATK', 'DEF', 'HP', 'Crit Rate', 0),
(7, 11, 'How do you &#039;Recall&#039; Catethieya’s Sword Shadows to trigger an Aero burst?', 'Using a Resonance Liberation.', 'Performing a Plunging Attack.', 'Executing a Perfect Dodge.', 'Swapping to another character.', 0),
(8, 14, 'check', 'a', 'b', 'e', 'e', 1),
(10, 17, 'What&#039;s reactor color?', 'red', 'green', 'yellow', 'blue', 2);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `UserID` int(11) NOT NULL,
  `FirstName` varchar(50) NOT NULL,
  `LastName` varchar(50) NOT NULL,
  `Email` varchar(100) NOT NULL,
  `PasswordHash` varchar(255) NOT NULL,
  `Role` enum('student','instructor','admin') NOT NULL,
  `Status` enum('active','pending','banned') DEFAULT 'active',
  `Phone` varchar(20) DEFAULT NULL,
  `Bio` text DEFAULT NULL,
  `ProfilePictureURL` varchar(255) DEFAULT NULL,
  `JoinedDate` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`UserID`, `FirstName`, `LastName`, `Email`, `PasswordHash`, `Role`, `Status`, `Phone`, `Bio`, `ProfilePictureURL`, `JoinedDate`) VALUES
(1, 'admin', 'r', 'admin@mail.com', '$2y$10$iAWzwq5.JCUYx5c4mpAgYuEU3iLR/XFg1A2ctoh7ndPVb6tnvyjMO', 'admin', 'active', NULL, NULL, NULL, '2026-04-12 10:00:06'),
(2, 'Aemeath', 'p', 'teacher@mail.com', '$2y$10$IIvxAHaevpXyGe4eb155U.0npRx9lXsYam8O5WMIDunCEAzW2qfj2', 'instructor', 'active', '', 'Group projects are like one works for 4. I don\'t support it :(', 'https://preview.redd.it/ww-3-1-aemeath-via-seele-v0-k2e3zrmaev9g1.jpeg?auto=webp&s=84e087ee71200a39e82fffe95722a3ea02d6aa4c', '2026-04-12 10:01:10'),
(3, 'lisa', 'lisa', 'lisa@mail.com', '$2y$10$Tqkvqovf1ZepB3qIHffHM.m6JI5LzlpdolxAlkF1d5J0n3gZ7SDwK', 'student', 'active', '', 'I don like team project', 'https://external-content.duckduckgo.com/iu/?u=https%3A%2F%2Fi.pinimg.com%2Foriginals%2Fe7%2Ffe%2Fe5%2Fe7fee55fbc4b8055dbe2dc364a63b8a2.jpg&f=1&nofb=1&ipt=4f30bf456d355d03f2eaaff6f913c4c841750e905b35e926ee8f4b4da431c6c1', '2026-04-12 10:01:20'),
(4, 'mm', 'mk', 'mark.stu@example.com', '$2y$10$.e5Trrs5qeiAdFi6SjuG2eJCLMRsfDuo4vQyp3tnx8tGx06rbCHxW', 'student', 'active', NULL, NULL, NULL, '2026-04-14 01:04:12'),
(5, 'Test', 'Student', 'teststudent@elp.com', '$2y$10$pqaP7Xj72t3Jek5Xi4XLZed8/JTP/9/a/3UgPk95ySt1tVaPwcTwi', 'student', 'active', NULL, NULL, NULL, '2026-04-14 01:34:34'),
(6, 'Math Prof', 'LinnSyuu', 'math@mail.com', '$2y$10$T/2yCKNYbnOEssdQR.ZoDezlZZLfly8NklmuwWg.dxJjPljxzpeZu', 'instructor', 'active', NULL, NULL, NULL, '2026-04-14 22:39:44'),
(7, 'Comp Sci ', 'Prof', 'comp@mail.com', '$2y$10$foEslRGQWJVpfc6FgzBQDeS556C63Fd0iHqzd9jJiMKIFL491W9ZW', 'instructor', 'active', NULL, NULL, NULL, '2026-04-14 22:44:41'),
(8, 'test1', 'r', 'test3@mail.com', '$2y$10$oEJOn1xlMq8A0gBaqy2Gl.yA/Q/vHOv0JHQxWv45yKHCsGEUStWMa', 'student', 'active', NULL, NULL, NULL, '2026-04-15 02:47:22');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `assessment`
--
ALTER TABLE `assessment`
  ADD PRIMARY KEY (`AssessmentID`),
  ADD KEY `CourseID` (`CourseID`);

--
-- Indexes for table `assessment_submission`
--
ALTER TABLE `assessment_submission`
  ADD PRIMARY KEY (`SubmissionID`),
  ADD KEY `AssessmentID` (`AssessmentID`),
  ADD KEY `StudentID` (`StudentID`);

--
-- Indexes for table `course`
--
ALTER TABLE `course`
  ADD PRIMARY KEY (`CourseID`),
  ADD KEY `InstructorID` (`InstructorID`);

--
-- Indexes for table `enrollment`
--
ALTER TABLE `enrollment`
  ADD PRIMARY KEY (`EnrollmentID`),
  ADD KEY `StudentID` (`StudentID`),
  ADD KEY `CourseID` (`CourseID`);

--
-- Indexes for table `lesson`
--
ALTER TABLE `lesson`
  ADD PRIMARY KEY (`LessonID`),
  ADD KEY `ModuleID` (`ModuleID`);

--
-- Indexes for table `moderation_report`
--
ALTER TABLE `moderation_report`
  ADD PRIMARY KEY (`ReportID`),
  ADD KEY `ReportedUserID` (`ReportedUserID`),
  ADD KEY `ReportedByUserID` (`ReportedByUserID`);

--
-- Indexes for table `module`
--
ALTER TABLE `module`
  ADD PRIMARY KEY (`ModuleID`),
  ADD KEY `CourseID` (`CourseID`);

--
-- Indexes for table `notification`
--
ALTER TABLE `notification`
  ADD PRIMARY KEY (`NotificationID`),
  ADD KEY `fk_user_notification` (`UserID`);

--
-- Indexes for table `question`
--
ALTER TABLE `question`
  ADD PRIMARY KEY (`QuestionID`),
  ADD KEY `AssessmentID` (`AssessmentID`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`UserID`),
  ADD UNIQUE KEY `Email` (`Email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `assessment`
--
ALTER TABLE `assessment`
  MODIFY `AssessmentID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `assessment_submission`
--
ALTER TABLE `assessment_submission`
  MODIFY `SubmissionID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `course`
--
ALTER TABLE `course`
  MODIFY `CourseID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `enrollment`
--
ALTER TABLE `enrollment`
  MODIFY `EnrollmentID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `lesson`
--
ALTER TABLE `lesson`
  MODIFY `LessonID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `moderation_report`
--
ALTER TABLE `moderation_report`
  MODIFY `ReportID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `module`
--
ALTER TABLE `module`
  MODIFY `ModuleID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `notification`
--
ALTER TABLE `notification`
  MODIFY `NotificationID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `question`
--
ALTER TABLE `question`
  MODIFY `QuestionID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `UserID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `assessment`
--
ALTER TABLE `assessment`
  ADD CONSTRAINT `assessment_ibfk_1` FOREIGN KEY (`CourseID`) REFERENCES `course` (`CourseID`) ON DELETE CASCADE;

--
-- Constraints for table `assessment_submission`
--
ALTER TABLE `assessment_submission`
  ADD CONSTRAINT `assessment_submission_ibfk_1` FOREIGN KEY (`AssessmentID`) REFERENCES `assessment` (`AssessmentID`) ON DELETE CASCADE,
  ADD CONSTRAINT `assessment_submission_ibfk_2` FOREIGN KEY (`StudentID`) REFERENCES `users` (`UserID`) ON DELETE CASCADE;

--
-- Constraints for table `course`
--
ALTER TABLE `course`
  ADD CONSTRAINT `course_ibfk_1` FOREIGN KEY (`InstructorID`) REFERENCES `users` (`UserID`) ON DELETE SET NULL;

--
-- Constraints for table `enrollment`
--
ALTER TABLE `enrollment`
  ADD CONSTRAINT `enrollment_ibfk_1` FOREIGN KEY (`StudentID`) REFERENCES `users` (`UserID`) ON DELETE CASCADE,
  ADD CONSTRAINT `enrollment_ibfk_2` FOREIGN KEY (`CourseID`) REFERENCES `course` (`CourseID`) ON DELETE CASCADE;

--
-- Constraints for table `lesson`
--
ALTER TABLE `lesson`
  ADD CONSTRAINT `lesson_ibfk_1` FOREIGN KEY (`ModuleID`) REFERENCES `module` (`ModuleID`) ON DELETE CASCADE;

--
-- Constraints for table `moderation_report`
--
ALTER TABLE `moderation_report`
  ADD CONSTRAINT `moderation_report_ibfk_1` FOREIGN KEY (`ReportedUserID`) REFERENCES `users` (`UserID`) ON DELETE CASCADE,
  ADD CONSTRAINT `moderation_report_ibfk_2` FOREIGN KEY (`ReportedByUserID`) REFERENCES `users` (`UserID`) ON DELETE SET NULL;

--
-- Constraints for table `module`
--
ALTER TABLE `module`
  ADD CONSTRAINT `module_ibfk_1` FOREIGN KEY (`CourseID`) REFERENCES `course` (`CourseID`) ON DELETE CASCADE;

--
-- Constraints for table `notification`
--
ALTER TABLE `notification`
  ADD CONSTRAINT `fk_user_notification` FOREIGN KEY (`UserID`) REFERENCES `users` (`UserID`) ON DELETE CASCADE;

--
-- Constraints for table `question`
--
ALTER TABLE `question`
  ADD CONSTRAINT `question_ibfk_1` FOREIGN KEY (`AssessmentID`) REFERENCES `assessment` (`AssessmentID`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
