<?php
/**
 * index.php — FoodFusion homepage
 */
require_once __DIR__ . '/config.php';

$pageTitle = 'FoodFusion — Cook. Share. Belong.';

/**
 * News feed (the "featured recipes & culinary trends" section).
 *
 * For now this returns sample data. To go live, swap the body of this
 * function for either:
 *   (a) a call to a food/news API (e.g. a curl request to an API,
 *       cached in the `news_cache` table so we're not hitting rate
 *       limits on every page load), or
 *   (b) a query against a `posts` table once the Community Cookbook
 *       / editorial pipeline exists.
 */
function getNewsItems(PDO $pdo): array
{
    // TODO: replace with real API/DB call. Example shape below.
    return [
        [
            'title'   => 'Five-Spice Braised Short Rib',
            'excerpt' => 'A slow Sunday project that rewards patience with fall-apart meat and a glossy, spiced sauce.',
            'tag'     => 'Featured Recipe',
            'image'   => 'https://images.unsplash.com/photo-1544025162-d76694265947?auto=format&fit=crop&w=600&q=80',
        ],
        [
            'title'   => 'Fermentation Is Having a Moment',
            'excerpt' => 'Home cooks are turning kitchen counters into fermentation stations. Here is where to start.',
            'tag'     => 'Culinary Trend',
            'image'   => 'https://images.unsplash.com/photo-1600335895229-6e75511892c8?auto=format&fit=crop&w=600&q=80',
        ],
        [
            'title'   => 'The Case for a Well-Worn Knife',
            'excerpt' => 'Why chefs trust an old blade over a new one, and how to bring yours back to life.',
            'tag'     => 'Culinary Tip',
            'image'   => 'https://images.unsplash.com/photo-1607877742574-a7253426f5aa?auto=format&fit=crop&w=600&q=80',
        ],
    ];
}

/**
 * Upcoming cooking events for the homepage carousel.
 * TODO: replace with a query against an `events` table (title, date,
 * location, image, description, RSVP count).
 */
function getUpcomingEvents(PDO $pdo): array
{
    return [
        [
            'title'  => 'Live: Weeknight Noodles',
            'date'   => 'Sat, 12 Sep',
            'desc'   => 'A 45-minute live cook-along for fast noodle dinners that don\'t taste rushed.',
            'image'  => 'https://images.unsplash.com/photo-1585032226651-759b368d7246?auto=format&fit=crop&w=700&q=80',
        ],
        [
            'title'  => 'Bread Basics Workshop',
            'date'   => 'Sun, 20 Sep',
            'desc'   => 'From slack dough to a proper crust — everything that goes wrong, and why.',
            'image'  => 'https://images.unsplash.com/photo-1509440159596-0249088772ff?auto=format&fit=crop&w=700&q=80',
        ],
        [
            'title'  => 'Knife Skills for Beginners',
            'date'   => 'Sat, 26 Sep',
            'desc'   => 'Grip, guard, and the four cuts that cover most of what a recipe will ask of you.',
            'image'  => 'https://images.unsplash.com/photo-1556910103-1c02745aae4d?auto=format&fit=crop&w=700&q=80',
        ],
        [
            'title'  => 'Community Potluck Night',
            'date'   => 'Fri, 2 Oct',
            'desc'   => 'Bring a dish from the Cookbook, meet other members, take home someone else\'s recipe.',
            'image'  => 'https://images.unsplash.com/photo-1529543544282-ea669407fca3?auto=format&fit=crop&w=700&q=80',
        ],
    ];
}

$newsItems = getNewsItems($pdo);
$events    = getUpcomingEvents($pdo);

require_once __DIR__ . '/includes/header.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <main id="main-content">

        <!-- HERO -->
        <section class="hero">
            <div class="hero-inner">
                <div class="hero-copy">
                    <p class="eyebrow">Home cooking, taken seriously</p>
                    <h1>Cook like the recipe is<br>the starting point, not the rule.</h1>
                    <p class="hero-lede">FoodFusion is a place for people who cook at home — to find recipes worth repeating, pick up real technique, and share what's working in their own kitchen with people who get it.</p>
                    <div class="hero-actions">
                        <button type="button" class="btn btn-primary btn-lg" id="heroJoinBtn">Join the community</button>
                        <a href="recipes.php" class="btn btn-outline btn-lg">Browse recipes</a>
                    </div>
                </div>
                <div class="hero-media" aria-hidden="true">
                    <img src="https://images.unsplash.com/photo-1476718406336-bb5a9690ee2a?auto=format&fit=crop&w=900&q=80" alt="">
                    <div class="hero-media-float">
                        <img src="https://images.unsplash.com/photo-1512621776951-a57141f2eefd?auto=format&fit=crop&w=400&q=80" alt="Fresh vegetable bowl">
                    </div>
                </div>
            </div>
        </section>

        <!-- MISSION -->
        <section class="mission">
            <div class="mission-inner">
                <h2>Why we're here</h2>
                <p>Recipe sites got crowded with ads and life stories. We built FoodFusion around three things: recipes that actually work in a normal kitchen, techniques explained clearly enough to stick, and a community that shares what they learn instead of hoarding it.</p>
            </div>
        </section>

        <!-- NEWS FEED -->
        <section class="news-feed" id="news">
            <div class="section-heading">
                <p class="eyebrow">From the kitchen</p>
                <h2>Featured recipes &amp; culinary trends</h2>
            </div>

            <div class="news-grid">
                <?php foreach ($newsItems as $item): ?>
                <article class="news-card">
                    <div class="news-card-img">
                        <img src="<?= htmlspecialchars($item['image']) ?>" alt="<?= htmlspecialchars($item['title']) ?>" loading="lazy">
                    </div>
                    <div class="news-card-body">
                        <p class="news-tag"><?= htmlspecialchars($item['tag']) ?></p>
                        <h3><?= htmlspecialchars($item['title']) ?></h3>
                        <p><?= htmlspecialchars($item['excerpt']) ?></p>
                        <a href="recipes.php" class="text-link">Read more</a>
                    </div>
                </article>
                <?php endforeach; ?>
            </div>
        </section>

        <!-- EVENTS CAROUSEL -->
        <section class="events" id="events">
            <div class="section-heading">
                <p class="eyebrow">Save the date</p>
                <h2>Upcoming cooking events</h2>
            </div>

            <div class="carousel" id="eventsCarousel" role="region" aria-label="Upcoming cooking events" aria-roledescription="carousel">
                <div class="carousel-track" id="carouselTrack">
                    <?php foreach ($events as $event): ?>
                    <div class="carousel-slide">
                        <article class="event-card">
                            <div class="event-card-img">
                                <img src="<?= htmlspecialchars($event['image']) ?>" alt="<?= htmlspecialchars($event['title']) ?>" loading="lazy">
                                <span class="event-date"><?= htmlspecialchars($event['date']) ?></span>
                            </div>
                            <div class="event-card-body">
                                <h3><?= htmlspecialchars($event['title']) ?></h3>
                                <p><?= htmlspecialchars($event['desc']) ?></p>
                                <a href="events.php" class="text-link">Reserve a spot</a>
                            </div>
                        </article>
                    </div>
                    <?php endforeach; ?>
                </div>

                <button type="button" class="carousel-arrow carousel-prev" id="carouselPrev" aria-label="Previous events">
                    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M15 6l-6 6 6 6"/></svg>
                </button>
                <button type="button" class="carousel-arrow carousel-next" id="carouselNext" aria-label="Next events">
                    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M9 6l6 6-6 6"/></svg>
                </button>

                <div class="carousel-dots" id="carouselDots"></div>
            </div>
        </section>

    </main>
</body>
</html>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
