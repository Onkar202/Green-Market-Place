-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 18, 2024 at 04:58 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `gmp`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `admin_id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `last_login` timestamp NULL DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`admin_id`, `username`, `password`, `email`, `full_name`, `created_at`, `last_login`, `is_active`) VALUES
(3, 'admin', '$2y$10$mqf736MMNKEFy/RFvirukeSlaSFu0ESpwI9iV3xpQx0KdEZlZF0pO', 'ronyk010201@gmail.com', 'onkar', '2024-10-17 06:44:46', '2024-10-18 14:54:33', 1),
(6, 'admin2', '$2y$10$j57QFXmlRpc1jFVm5hOLCuC6ogXWjuTswbCRv2yMQe17DWlZUxLJ6', 'ronyk0102031@gmail.com', 'onkar', '2024-10-18 05:04:43', '2024-10-18 14:38:28', 1),
(9, 'admin4', '$2y$10$BIEZCVzZ87NgfOKMuFYrFenLub.HbsYTuYXHXWRuHzdYOKp3K2ED6', 'ronyk0102401@gmail.com', 'onkar', '2024-10-18 05:19:58', NULL, 1);

-- --------------------------------------------------------

--
-- Table structure for table `admin_logs`
--

CREATE TABLE `admin_logs` (
  `log_id` int(11) NOT NULL,
  `admin_id` int(11) NOT NULL,
  `action` varchar(255) NOT NULL,
  `action_time` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin_logs`
--

INSERT INTO `admin_logs` (`log_id`, `admin_id`, `action`, `action_time`) VALUES
(1, 3, 'logout', '2024-10-18 11:18:53'),
(2, 3, 'logout', '2024-10-18 11:20:21'),
(3, 3, 'logout', '2024-10-18 11:20:38'),
(4, 3, 'logout', '2024-10-18 11:22:45'),
(5, 3, 'logout', '2024-10-18 20:08:19'),
(6, 6, 'logout', '2024-10-18 20:24:07');

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(10) NOT NULL,
  `cat_id` varchar(30) NOT NULL,
  `cat_name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `cat_id`, `cat_name`) VALUES
(2, '002', 'Vegetables'),
(1, '01', 'Fruits'),
(9, '10', 'roots'),
(10, '11', 'bulbs'),
(11, '120', 'Seeds'),
(14, '123', 'Flowers'),
(16, '14', 'Milk Products'),
(13, '200', 'pods'),
(15, '21', 'Tubers');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `order_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `payment_method` varchar(50) NOT NULL,
  `address` text NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `total_quantity` int(11) NOT NULL DEFAULT 0,
  `total_price` decimal(10,2) NOT NULL DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`order_id`, `user_id`, `payment_method`, `address`, `created_at`, `total_quantity`, `total_price`) VALUES
(68, 8, 'Credit Card', 'sangli\r\nkolhapur', '2024-10-18 20:09:32', 15, 1550.00),
(71, 8, 'Credit Card', 'sangli\r\nkolhapur', '2024-10-18 20:16:47', 9, 270.00);

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` varchar(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `total_price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `quantity`, `price`, `total_price`) VALUES
(1, 68, '2', 1, 30.00, 30.00),
(2, 68, '3', 4, 30.00, 120.00),
(3, 68, '4', 3, 100.00, 300.00),
(4, 68, '90', 3, 100.00, 300.00),
(5, 68, '7', 4, 200.00, 800.00),
(6, 71, '2', 2, 30.00, 60.00),
(7, 71, '3', 7, 30.00, 210.00);

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `pr_id` varchar(50) NOT NULL,
  `pr_name` varchar(255) NOT NULL,
  `pr_quantity` int(11) NOT NULL,
  `pr_category` varchar(30) NOT NULL,
  `pr_price` int(11) NOT NULL,
  `seller_id` int(11) NOT NULL,
  `pr_img` varchar(255) NOT NULL,
  `pr_desc` varchar(10000) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `pr_id`, `pr_name`, `pr_quantity`, `pr_category`, `pr_price`, `seller_id`, `pr_img`, `pr_desc`) VALUES
(4, '2', 'spinich', 52, '2', 30, 1, 'uploads/spinich.jpeg', 'Rich in Nutrients: Spinach is packed with vitamins (A, C, K, and folate), minerals (iron, calcium, magnesium), and antioxidants that support overall health.\r\n\r\nBoosts Eye Health: It contains lutein and zeaxanthin, antioxidants that help protect your eyes from damage caused by UV light and may reduce the risk of macular degeneration.\r\n\r\nSupports Bone Health: Spinach is a good source of vitamin K, which is essential for maintaining healthy bones and improving calcium absorption.\r\n\r\nImproves Heart Health: Its high potassium content helps regulate blood pressure, and its fiber helps lower cholesterol levels.\r\n\r\nAids Digestion: Spinach is rich in dietary fiber, which promotes healthy digestion and prevents constipation.\r\n\r\nEnhances Muscle Function: Magnesium and nitrate found in spinach help in muscle function and can improve exercise performance.\r\n\r\nSupports Immune Function: Vitamin C in spinach boosts immune system function, helping the body fight infections.\r\n\r\nAnti-Inflammatory Properti'),
(5, '3', 'Tomato', 41, '1', 30, 3, 'uploads/tomato.jpeg', 'Tomatoes are packed with nutrients that offer a wide range of health benefits. Here are some key benefits of tomatoes and how to incorporate them into your diet:\r\n\r\n### Health Benefits of Tomatoes:\r\n1. **Rich in Antioxidants:**\r\n   - Tomatoes are a great source of *lycopene*, a powerful antioxidant that helps combat free radicals in the body, which can reduce the risk of certain chronic diseases, including cancer.\r\n\r\n2. **Supports Heart Health:**\r\n   - Lycopene, along with *potassium* and *vitamin C*, promotes heart health by helping to lower blood pressure, reduce cholesterol levels, and improve blood vessel function.\r\n\r\n3. **Boosts Skin Health:**\r\n   - The vitamin C in tomatoes supports collagen production, which keeps skin firm and reduces signs of aging. Lycopene also protects the skin from UV damage.\r\n\r\n4. **Aids Digestion:**\r\n   - Tomatoes are high in *fiber*, which promotes healthy digestion and can help prevent constipation.\r\n\r\n5. **Improves Vision:**\r\n   - Tomatoes contain *vitamin A* and *beta-carotene*, both essential for maintaining good eyesight and preventing night blindness.\r\n\r\n6. **Supports Bone Health:**\r\n   - Tomatoes contain *vitamin K* and *calcium*, both important for maintaining strong bones and preventing bone-related diseases.\r\n\r\n7. **Enhances Immune System:**\r\n   - The high levels of *vitamin C* strengthen the immune system, helping to protect against common illnesses such as colds and flu.\r\n\r\n### Ways to Use Tomatoes:\r\n1. **Raw:**\r\n   - Eat tomatoes raw in salads or as a snack. Just wash and slice them to enjoy the fresh flavor and nutrients.\r\n\r\n2. **Sauce or Soup:**\r\n   - Make tomato sauce for pasta or pizza, or prepare a tomato-based soup. Cooking tomatoes enhances the absorption of lycopene.\r\n\r\n3. **Juice:**\r\n   - Tomato juice is a refreshing and nutrient-rich drink. You can drink it on its own or mix it with other vegetable juices.\r\n\r\n4. **Grilled or Roasted:**\r\n   - Grilled or roasted tomatoes bring out their natural sweetness and add depth to dishes like sandwiches, tacos, or grilled vegetables.\r\n\r\n5. **Salsa or Chutney:**\r\n   - Tomatoes can be used to make a fresh salsa or chutney to complement grilled meats or to be eaten with chips or bread.\r\n\r\n6. **In Sandwiches or Wraps:**\r\n   - Add slices of fresh tomato to sandwiches or wraps for extra flavor and nutrients.\r\n\r\n7. **Canned Tomatoes:**\r\n   - Keep canned tomatoes on hand for making stews, curries, or sauces when fresh tomatoes are out of season.\r\n\r\nIncorporating tomatoes into your daily meals can provide a delicious and nutritious boost to your diet!'),
(6, '4', 'Mango', 75, '01', 100, 2, 'uploads/mango.jpeg', 'Mangoes are not only delicious but also packed with a range of health benefits due to their high nutritional value. Here’s an overview of their health benefits and ways to enjoy them:\r\n\r\n### Health Benefits of Mango:\r\n1. **Rich in Nutrients:**\r\n   - Mangoes are a great source of essential vitamins and minerals. They are particularly high in **Vitamin C** (improves immune function) and **Vitamin A** (supports eye health).\r\n   - They also provide folate, vitamin K, vitamin E, and various B vitamins.\r\n\r\n2. **Boosts Immunity:**\r\n   - Mangoes are packed with antioxidants, especially **beta-carotene** and **vitamin C**, which help protect against free radicals and boost immune system function.\r\n\r\n3. **Promotes Digestive Health:**\r\n   - Mangoes contain dietary **fiber** that supports digestion and helps prevent constipation.\r\n   - They also have **enzymes like amylases** that help break down complex carbohydrates, making digestion easier.\r\n\r\n4. **Supports Heart Health:**\r\n   - The high levels of **potassium** and **magnesium** in mangoes help maintain healthy blood pressure and heart function.\r\n   - **Antioxidants** like quercetin, mangiferin, and astragalin help protect the heart by reducing inflammation and oxidative stress.\r\n\r\n5. **Improves Skin Health:**\r\n   - The **vitamin A** and **vitamin C** in mangoes promote collagen production, keeping skin firm and reducing signs of aging.\r\n   - The antioxidants also help in reducing acne and providing a natural glow.\r\n\r\n6. **Good for Eye Health:**\r\n   - The **vitamin A** content helps in maintaining good vision, preventing dryness of the eyes, and reducing the risk of night blindness.\r\n\r\n7. **May Help Lower Cholesterol:**\r\n   - Mangoes are rich in **pectin**, a type of soluble fiber, which helps lower LDL (bad cholesterol) levels.\r\n\r\n### How to Eat Mango:\r\n1. **Fresh Mango:**\r\n   - Simply peel the mango and slice the flesh. You can eat the juicy slices directly or cut them into cubes.\r\n\r\n2. **In Smoothies:**\r\n   - Mangoes are a great base for smoothies. Blend fresh mango pieces with yogurt, milk, or other fruits like bananas or berries for a refreshing drink.\r\n\r\n3. **Mango Salsa:**\r\n   - Chop mangoes and mix them with onions, cilantro, lime juice, and a little chili for a flavorful salsa. It’s perfect as a side dish with grilled fish or chicken.\r\n\r\n4. **Mango Salad:**\r\n   - Add mango cubes to a fresh salad with greens, cucumber, avocado, and a light vinaigrette dressing.\r\n\r\n5. **Mango with Yogurt:**\r\n   - Mango pairs well with plain or flavored yogurt. Top yogurt with diced mango and a sprinkle of granola for a healthy snack or breakfast.\r\n\r\n6. **Mango Chutney:**\r\n   - Cook mangoes with spices like cumin, mustard seeds, and sugar to create a sweet and tangy chutney, which can be enjoyed with bread, grilled meats, or cheese.\r\n\r\n7. **Dried Mango:**\r\n   - Dried mango slices can be a healthy, portable snack. Just make sure they don’t have added sugar.\r\n\r\n8. **Frozen Mango:**\r\n   - Freeze mango slices and use them as ice cubes in smoothies, drinks, or as a refreshing snack on hot days.\r\n\r\n### Conclusion:\r\nMangoes offer a wealth of health benefits, from boosting immunity to improving skin health. Whether you enjoy them fresh, in a salad, or blended into a smoothie, mangoes are a versatile fruit that can be easily incorporated into your diet.'),
(7, '5', 'Potato', 100, '21', 100, 3, 'uploads/potato.jpeg', 'Potatoes are a widely consumed and versatile food, known for their rich nutritional content. They offer several health benefits when consumed as part of a balanced diet.\r\n\r\n### Health Benefits of Potatoes:\r\n\r\n1. **Rich in Nutrients:**\r\n   - Potatoes are a good source of essential nutrients like **vitamin C**, **vitamin B6**, and **potassium**.\r\n   - They also provide **fiber**, especially when eaten with the skin, which supports digestive health.\r\n\r\n2. **High in Antioxidants:**\r\n   - Potatoes contain compounds like **flavonoids, carotenoids, and phenolic acids**, which act as antioxidants to neutralize harmful free radicals, reducing the risk of chronic diseases like heart disease.\r\n\r\n3. **Supports Digestive Health:**\r\n   - The fiber in potatoes, especially in their skin, helps promote regular bowel movements and prevents constipation.\r\n   - Potatoes contain **resistant starch**, which serves as food for beneficial gut bacteria, supporting a healthy digestive system.\r\n\r\n4. **Promotes Heart Health:**\r\n   - The high levels of **potassium** help regulate blood pressure by balancing out the negative effects of sodium.\r\n   - **Fiber** in potatoes can also help lower cholesterol levels, promoting heart health.\r\n\r\n5. **Improves Bone Health:**\r\n   - Potatoes contain nutrients such as **phosphorus**, **magnesium**, and **calcium**, all of which contribute to bone health and strength.\r\n\r\n6. **Boosts Immune Function:**\r\n   - The **vitamin C** content in potatoes supports immune health by aiding in collagen production, speeding up wound healing, and protecting cells from oxidative stress.\r\n\r\n7. **Energy Boost:**\r\n   - Potatoes are rich in **carbohydrates**, making them a great source of energy. They are ideal for people with active lifestyles, athletes, or those needing an energy boost.\r\n\r\n8. **Aids in Weight Management:**\r\n   - While often misunderstood as \"fattening,\" boiled or baked potatoes (without heavy toppings) can be a low-calorie, filling food due to their high water and fiber content, which promotes satiety.\r\n\r\n### How to Eat Potatoes:\r\n\r\n1. **Boiled Potatoes:**\r\n   - Boil whole potatoes or cut them into pieces. They can be eaten plain, with herbs and a dash of olive oil, or mashed for a healthier version of mashed potatoes.\r\n\r\n2. **Baked Potatoes:**\r\n   - Baking potatoes retains their nutrients and enhances their flavor. Top with healthy toppings like plain yogurt, steamed vegetables, or a sprinkle of herbs.\r\n\r\n3. **Mashed Potatoes:**\r\n   - A classic comfort food, mashed potatoes can be made healthier by using olive oil or low-fat milk instead of butter and cream.\r\n\r\n4. **Potato Salad:**\r\n   - Boil potatoes and mix with a dressing made from olive oil, mustard, vinegar, and herbs for a light potato salad.\r\n\r\n5. **Roasted Potatoes:**\r\n   - Cut potatoes into wedges, drizzle with olive oil, and season with herbs. Roast in the oven for a crispy, flavorful side dish.\r\n\r\n6. **Potato Soup:**\r\n   - Potatoes can be used to make hearty, creamy soups. Combine with vegetables and broth for a nutritious meal.\r\n\r\n7. **Potato Stir-fry:**\r\n   - Thinly slice or cube potatoes and stir-fry them with other vegetables and your favorite seasonings for a quick and delicious meal.\r\n\r\n8. **Steamed or Grilled Potatoes:**\r\n   - Steaming or grilling potatoes retains their nutrients and makes for a light and tasty dish. You can grill them with other vegetables or enjoy them steamed with a sprinkle of salt and pepper.\r\n\r\n### Conclusion:\r\nPotatoes are a nutritious and versatile food, offering many health benefits when eaten in moderation and prepared in healthy ways. Whether baked, boiled, or roasted, potatoes can easily be incorporated into a variety of meals for their flavor and nutritional value.'),
(8, '6', 'Carrot', 87, '10', 100, 2, 'uploads/carrot.jpeg', 'Carrots are highly nutritious and provide a wide range of health benefits. They are rich in essential vitamins, minerals, and antioxidants, making them a great addition to a healthy diet.\r\n\r\n### Health Benefits of Carrots:\r\n\r\n1. **Rich in Nutrients:**\r\n   - Carrots are an excellent source of **beta-carotene**, which the body converts into **vitamin A**. Vitamin A is essential for **eye health**, immune function, and skin health.\r\n   - Carrots also provide **vitamin K1**, **potassium**, **fiber**, and **antioxidants**.\r\n\r\n2. **Promotes Eye Health:**\r\n   - Carrots are well-known for supporting eye health due to their high levels of **beta-carotene**. Beta-carotene helps reduce the risk of macular degeneration and **night blindness**.\r\n\r\n3. **Supports Digestive Health:**\r\n   - Carrots are a great source of **dietary fiber**, which helps regulate digestion, prevents constipation, and promotes regular bowel movements.\r\n\r\n4. **Boosts Immune System:**\r\n   - The high **antioxidant** content in carrots, including vitamin C and beta-carotene, helps protect the body from harmful free radicals and supports immune system function.\r\n\r\n5. **Improves Skin Health:**\r\n   - Carrots contain **vitamin A** and antioxidants that help maintain healthy skin by preventing acne, dryness, and other skin issues. They also support collagen production, which keeps the skin firm and youthful.\r\n\r\n6. **May Help Lower Cholesterol:**\r\n   - Studies have shown that eating carrots may help lower **LDL (bad) cholesterol** levels, reducing the risk of heart disease.\r\n\r\n7. **Aids in Weight Management:**\r\n   - Carrots are low in calories and high in fiber, making them a filling, nutrient-dense food that can help control appetite and support weight loss efforts.\r\n\r\n8. **Promotes Heart Health:**\r\n   - The **potassium** in carrots helps regulate blood pressure, while the fiber helps reduce cholesterol levels. Together, these benefits support a healthy heart and lower the risk of cardiovascular disease.\r\n\r\n9. **Anticancer Properties:**\r\n   - Carrots contain **carotenoids** and **polyacetylenes**, which are plant compounds believed to have anticancer properties. Regular consumption of carrots has been linked to a reduced risk of certain cancers, including lung, colorectal, and prostate cancer.\r\n\r\n### How to Eat Carrots:\r\n\r\n1. **Raw Carrots:**\r\n   - Raw carrots make a crunchy, nutritious snack. You can eat them on their own, dip them in hummus, or add them to salads for extra texture.\r\n\r\n2. **Cooked Carrots:**\r\n   - Boiled, steamed, or roasted carrots are delicious and maintain their nutritional value. Cooking can even enhance the absorption of beta-carotene.\r\n\r\n3. **Carrot Juice:**\r\n   - Freshly squeezed carrot juice is a refreshing and nutrient-packed beverage. You can combine it with other fruits and vegetables like apples or ginger for added flavor and health benefits.\r\n\r\n4. **Carrot Salad:**\r\n   - Grate carrots and mix them with lemon juice, olive oil, and herbs for a simple, refreshing carrot salad. You can also add raisins or nuts for more flavor and texture.\r\n\r\n5. **Carrot Soup:**\r\n   - Pureed carrot soup, often blended with ginger or coconut milk, is a creamy and flavorful dish packed with nutrition.\r\n\r\n6. **Carrot Stir-fry:**\r\n   - Thinly slice carrots and stir-fry them with other vegetables and seasonings. This is a quick and easy way to incorporate carrots into your meals.\r\n\r\n7. **Roasted Carrots:**\r\n   - Toss carrots in olive oil and season with herbs or spices, then roast them in the oven for a caramelized, slightly sweet side dish.\r\n\r\n8. **Carrot Muffins or Cakes:**\r\n   - Carrots add moisture and natural sweetness to baked goods like carrot muffins, bread, or the classic carrot cake.\r\n\r\n9. **Pickled Carrots:**\r\n   - Pickling carrots with vinegar, spices, and herbs makes for a tangy, crunchy side dish or snack.\r\n\r\n### Conclusion:\r\nCarrots are versatile, tasty, and packed with vitamins, minerals, and antioxidants. Whether you enjoy them raw, cooked, juiced, or baked into a cake, carrots are a nutritious addition to any diet, offering benefits for your eyes, skin, heart, and overall health.\r\n'),
(9, '90', 'Curd', 12, '14', 100, 4, 'uploads/Curd_rice_898.jpg', 'Curd, also known as yogurt or dahi, is a fermented dairy product with numerous health benefits. It\'s made by fermenting milk with beneficial bacteria like **Lactobacillus**. Curd is a staple in many diets around the world due to its nutritional value and versatility.\r\n\r\n### Health Benefits of Curd:\r\n\r\n1. **Promotes Digestive Health:**\r\n   - Curd is rich in **probiotics**, which are beneficial bacteria that promote gut health. These probiotics help maintain a healthy balance of bacteria in the digestive tract, improving digestion and preventing issues like bloating, constipation, and diarrhea.\r\n\r\n2. **Strengthens Immune System:**\r\n   - The probiotics found in curd help boost the immune system by enhancing the body\'s natural defense mechanisms. Regular consumption of curd can help protect against infections.\r\n\r\n3. **Rich Source of Calcium:**\r\n   - Curd is an excellent source of **calcium**, which is essential for maintaining strong bones and teeth. Regular intake of curd can help prevent **osteoporosis** and support overall bone health.\r\n\r\n4. **Aids in Weight Management:**\r\n   - Curd is low in calories and high in protein, making it a filling and satisfying food. The protein in curd helps regulate appetite and promotes a feeling of fullness, aiding in weight loss or weight management.\r\n\r\n5. **Good for Heart Health:**\r\n   - The probiotics in curd have been shown to help lower **bad cholesterol (LDL)** and regulate blood pressure, reducing the risk of heart disease. Curd can also help maintain **healthy cholesterol levels**.\r\n\r\n6. **Improves Skin Health:**\r\n   - The nutrients and probiotics in curd can benefit the skin by improving its moisture, texture, and elasticity. Applying curd topically can soothe skin irritation and provide a natural glow, while eating it supports skin health from within.\r\n\r\n7. **Lactose Intolerance Friendly:**\r\n   - People with lactose intolerance may find curd easier to digest than milk because the lactose in curd is partially broken down by the fermentation process. This makes curd a good alternative for those who cannot tolerate milk.\r\n\r\n8. **Promotes Heart Health:**\r\n   - Curd is rich in **potassium**, which helps regulate blood pressure and reduce the risk of cardiovascular diseases. The calcium and magnesium in curd also support heart health.\r\n\r\n9. **Improves Mental Health:**\r\n   - Probiotics in curd may have positive effects on mental health by improving the gut-brain connection. Some studies suggest that regular consumption of curd can help reduce symptoms of anxiety and depression.\r\n\r\n### How to Eat Curd:\r\n\r\n1. **Plain Curd:**\r\n   - Enjoy a bowl of plain curd on its own or with a sprinkle of salt or sugar, depending on your taste preference. It\'s refreshing and can be eaten at any time of the day.\r\n\r\n2. **Curd with Fruits:**\r\n   - Mix curd with fresh fruits like bananas, berries, or mangoes for a nutritious snack or dessert. You can also drizzle honey or add nuts for extra flavor and texture.\r\n\r\n3. **Curd with Honey:**\r\n   - A simple and delicious way to enjoy curd is to mix it with a spoonful of honey. This adds natural sweetness and makes for a healthy treat.\r\n\r\n4. **Raita:**\r\n   - Raita is a popular Indian dish made by mixing curd with chopped vegetables like cucumbers, onions, and tomatoes, along with spices like cumin, salt, and pepper. It’s often served as a side dish with spicy foods to cool the palate.\r\n\r\n5. **Smoothies:**\r\n   - Blend curd with fruits, vegetables, and a little water or milk to make a creamy smoothie. This is a great way to pack nutrients into a delicious drink.\r\n\r\n6. **Buttermilk (Chaas):**\r\n   - Buttermilk is a popular drink made by thinning curd with water and adding spices like cumin, black salt, and mint. It’s refreshing and helps cool the body, especially in hot weather.\r\n\r\n7. **Curd in Salads:**\r\n   - Use curd as a healthy dressing for salads instead of mayonnaise. It adds creaminess without the extra calories and fats.\r\n\r\n8. **Curd in Marinades:**\r\n   - Curd is commonly used in marinades for meats and vegetables. The lactic acid in curd helps tenderize the meat and enhances flavor.\r\n\r\n9. **Curd-based Desserts:**\r\n   - Curd can be used in a variety of desserts like **Shrikhand**, a sweetened and flavored curd dish, or added to fruit salads for a creamy texture.\r\n\r\n### Conclusion:\r\nCurd is a nutritious and versatile food that provides numerous health benefits, including improved digestion, stronger immunity, and better heart and bone health. Whether eaten plain, mixed with fruits, or used in recipes, curd is an excellent addition to a balanced diet.'),
(10, '7', 'Paneer', 36, '14', 200, 4, 'uploads/paneer.jpeg', 'Paneer, also known as Indian cottage cheese, is a fresh, non-aged, and non-melting cheese made by curdling milk with an acidic agent like lemon juice or vinegar. It\'s a staple in many Indian vegetarian dishes and is highly valued for its versatility and nutritional benefits.\r\n\r\n### Health Benefits of Paneer:\r\n\r\n1. **Rich Source of Protein:**\r\n   - Paneer is an excellent source of high-quality protein, making it a great option for vegetarians to meet their daily protein needs. Protein is essential for building and repairing muscles, tissues, and cells.\r\n\r\n2. **Supports Bone Health:**\r\n   - Paneer is rich in **calcium** and **phosphorus**, which are critical for maintaining strong bones and teeth. Regular consumption of paneer can help prevent bone-related issues like osteoporosis.\r\n\r\n3. **Aids in Weight Management:**\r\n   - Paneer is low in carbohydrates but high in protein and healthy fats, making it filling and ideal for those trying to lose or manage weight. It keeps you full for longer and helps prevent overeating.\r\n\r\n4. **Boosts Immune System:**\r\n   - Paneer contains several essential vitamins and minerals, including **vitamin D** and **B-complex vitamins** like riboflavin and B12, which help strengthen the immune system.\r\n\r\n5. **Good for Heart Health:**\r\n   - While paneer contains fats, it\'s also rich in healthy fats, including omega-3 fatty acids, which can help reduce bad cholesterol (LDL) levels. Moderate consumption of paneer can support heart health by regulating cholesterol levels and blood pressure.\r\n\r\n6. **Improves Digestion:**\r\n   - Paneer contains magnesium, which helps in maintaining a healthy digestive system by preventing constipation. Additionally, it’s easy to digest and suitable for people with lactose intolerance, as it has lower lactose levels compared to milk.\r\n\r\n7. **Prevents Blood Sugar Spikes:**\r\n   - Paneer has a low glycemic index (GI), which means it does not cause rapid spikes in blood sugar levels. It is beneficial for people with **diabetes** to include paneer in their diet to regulate their blood sugar levels.\r\n\r\n8. **Promotes Healthy Skin:**\r\n   - Paneer is packed with antioxidants, vitamins, and selenium, which contribute to better skin health. These nutrients protect the skin from free radical damage, delay the signs of aging, and promote glowing, healthy skin.\r\n\r\n9. **Improves Mental Health:**\r\n   - The vitamin B12 in paneer helps maintain the proper functioning of the nervous system and brain. It is essential for cognitive health and may help improve memory and reduce the risk of neurological disorders.\r\n\r\n10. **Promotes Muscle Growth:**\r\n    - Due to its high protein content, paneer is excellent for muscle building and recovery. It is particularly beneficial for athletes and bodybuilders to repair muscle tissues after workouts.\r\n\r\n### How to Eat Paneer:\r\n\r\n1. **Raw or Plain Paneer:**\r\n   - Paneer can be eaten raw as a snack. Just cut it into cubes or slices and sprinkle a little salt and pepper, or eat it with chutney or sauces for added flavor.\r\n\r\n2. **Paneer Bhurji:**\r\n   - Paneer bhurji is a scrambled paneer dish made by crumbling paneer and sautéing it with onions, tomatoes, green chilies, and spices. It\'s a quick and delicious dish often served with bread or roti.\r\n\r\n3. **Paneer Tikka:**\r\n   - Paneer tikka is a popular appetizer where paneer cubes are marinated in yogurt, spices, and herbs and then grilled or baked. It\'s a smoky, flavorful dish served with mint chutney.\r\n\r\n4. **Paneer in Curries:**\r\n   - Paneer is widely used in rich, creamy Indian curries like **Paneer Butter Masala**, **Shahi Paneer**, or **Palak Paneer** (paneer in spinach gravy). These dishes are often paired with naan or rice.\r\n\r\n5. **Paneer Paratha:**\r\n   - Paneer is stuffed into whole wheat dough to make paneer parathas, a popular North Indian flatbread. It is usually served with yogurt or pickles.\r\n\r\n6. **Paneer Salads:**\r\n   - Add cubed paneer to salads for a protein boost. Paneer pairs well with fresh vegetables, herbs, and light dressings like olive oil, lemon, or balsamic vinegar.\r\n\r\n7. **Paneer Wraps and Rolls:**\r\n   - Paneer is often used as a filling in wraps and rolls, along with vegetables and sauces. Paneer wraps are a great on-the-go meal and can be packed with nutrients.\r\n\r\n8. **Paneer Sandwiches:**\r\n   - Paneer makes for a great sandwich filling. Simply grill or sauté paneer slices and place them between slices of bread with your choice of vegetables and sauces.\r\n\r\n9. **Paneer Stir-fry:**\r\n   - Paneer can be stir-fried with vegetables, garlic, and soy sauce to make a simple and tasty stir-fry. It’s a healthy option for a quick meal.\r\n\r\n10. **Paneer Desserts:**\r\n    - Paneer is used in Indian desserts like **Rasgulla**, **Rasmalai**, and **Sandesh**. These are sweet treats where paneer is shaped into balls or cubes and soaked in sugar syrup or flavored milk.\r\n\r\n### Conclusion:\r\nPaneer is not only a delicious ingredient but also a highly nutritious one. It offers a wealth of health benefits, from supporting muscle growth and weight management to improving heart, bone, and skin health. Whether eaten raw, cooked in curries, or used in desserts, paneer is a versatile and healthy addition to any diet.');

-- --------------------------------------------------------

--
-- Table structure for table `sellers`
--

CREATE TABLE `sellers` (
  `id` int(11) NOT NULL,
  `seller_id` varchar(50) NOT NULL,
  `seller_name` varchar(100) NOT NULL,
  `address` varchar(255) NOT NULL,
  `address2` varchar(255) DEFAULT NULL,
  `city` varchar(100) NOT NULL,
  `state` varchar(100) NOT NULL,
  `zip` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sellers`
--

INSERT INTO `sellers` (`id`, `seller_id`, `seller_name`, `address`, `address2`, `city`, `state`, `zip`) VALUES
(1, '1', 'mayur', 'sangli', 'kolhapur', 'wangi', '28', '416001'),
(2, '2', 'Aslam', 'kolhapur', 'sangli', 'wangi', '7', '416001'),
(5, '3', 'Pavan', 'Ichkalranji', 'kolhapur', 'wangi', '2', '415316'),
(6, '4', 'Aditya', 'khanapur', 'sangli', 'atpadi', '8', '415102');

-- --------------------------------------------------------

--
-- Table structure for table `states`
--

CREATE TABLE `states` (
  `state_id` int(11) NOT NULL,
  `state_name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `states`
--

INSERT INTO `states` (`state_id`, `state_name`) VALUES
(1, 'Andhra Pradesh'),
(2, 'Arunachal Pradesh'),
(3, 'Assam'),
(4, 'Bihar'),
(5, 'Chhattisgarh'),
(6, 'Goa'),
(7, 'Gujarat'),
(8, 'Haryana'),
(9, 'Himachal Pradesh'),
(10, 'Jharkhand'),
(11, 'Karnataka'),
(12, 'Kerala'),
(13, 'Madhya Pradesh'),
(14, 'Maharashtra'),
(15, 'Manipur'),
(16, 'Meghalaya'),
(17, 'Mizoram'),
(18, 'Nagaland'),
(19, 'Odisha'),
(20, 'Punjab'),
(21, 'Rajasthan'),
(22, 'Sikkim'),
(23, 'Tamil Nadu'),
(24, 'Telangana'),
(25, 'Tripura'),
(26, 'Uttar Pradesh'),
(27, 'Uttarakhand'),
(28, 'West Bengal'),
(29, 'Andaman and Nicobar Islands'),
(30, 'Chandigarh'),
(31, 'Dadra and Nagar Haveli and Daman and Diu'),
(32, 'Lakshadweep'),
(33, 'Delhi'),
(34, 'Puducherry');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `Name` varchar(255) NOT NULL,
  `user_name` varchar(100) NOT NULL,
  `Email` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `address` varchar(255) NOT NULL,
  `Mobile` varchar(20) DEFAULT NULL,
  `city` varchar(100) NOT NULL,
  `state` int(11) NOT NULL,
  `zip` varchar(10) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `Name`, `user_name`, `Email`, `password`, `address`, `Mobile`, `city`, `state`, `zip`, `created_at`) VALUES
(3, 'onkar', 'kingrk', 'ronyk010201@gmail.com', '$2y$10$GHLaYD/UiE3e6lWool8.LOM8ePcHg4dQKiCZjyNOxijvaqDtboJtm', 'sangli', '8765436785', 'sangli', 14, '415316', '2024-10-17 04:27:13'),
(8, 'onkar', 'kingrk_22', 'onkarkumbhar125@gmail.com', '$2y$10$14dO3mfNWdBwaw2JmVGZOesWaxDzf0eyiDUKpak6Aa52i6DrFkuUq', 'sangli', '08765436786', 'sangli', 14, '415316', '2024-10-17 04:48:49');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`admin_id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `admin_logs`
--
ALTER TABLE `admin_logs`
  ADD PRIMARY KEY (`log_id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `cat_id` (`cat_id`,`cat_name`),
  ADD UNIQUE KEY `cat_id_2` (`cat_id`,`cat_name`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`order_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `pr_id` (`pr_id`);

--
-- Indexes for table `sellers`
--
ALTER TABLE `sellers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `seller_id` (`seller_id`);

--
-- Indexes for table `states`
--
ALTER TABLE `states`
  ADD PRIMARY KEY (`state_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_name` (`user_name`,`Email`,`Mobile`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `admin_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `admin_logs`
--
ALTER TABLE `admin_logs`
  MODIFY `log_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=72;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `sellers`
--
ALTER TABLE `sellers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `states`
--
ALTER TABLE `states`
  MODIFY `state_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`),
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`pr_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
