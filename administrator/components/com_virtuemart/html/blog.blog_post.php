<?php
defined('_VALID_MOS') or die('Direct Access to this location is not allowed.');
mm_showMyFileName(__FILE__);

global $vendor_currency, $my, $database, $VM_LANG, $mosConfig_live_site, $mosConfig_lang, $sef;

$userId = $my->id;
$slug = mosgetparam($_REQUEST, 'slug', '');

$page = 1;
if (preg_match('/perpage=(\d+)/i', $slug, $matches)) {
    $page = (int) $matches[1];
    $slug = '';
}

$limit = 6;
$offset = ($page - 1) * $limit;


$totalQuery = "SELECT COUNT(*) FROM jos_vm_blog_posts";
$database->setQuery($totalQuery);
$totalPosts = $database->loadResult();
$totalPages = ceil($totalPosts / $limit);

$queryOrder = "
SELECT 
    bp.id,
    bt.slug,
    bt.title,
    bt.description,
    bt. short_description,
    pi.thumb_link AS thumb_link,
    bp.is_published,
    u.username,
    bp.created_at
FROM jos_vm_blog_posts AS bp
LEFT JOIN jos_vm_blog_post_data AS bt ON bt.post_id = bp.id 
LEFT JOIN jos_vm_blog_post_images AS pi ON pi.blog_post_data_id = bt.id
LEFT JOIN jos_users u ON u.id = bp.creator_id
WHERE bp.is_published = 1";

if ($slug) {
    $queryOrder .= " AND bt.slug = '$slug'";
}

$queryOrder .= " ORDER BY bp.created_at DESC LIMIT $offset, $limit";
$database->setQuery($queryOrder);
$blogPosts = $database->loadObjectList();

if ($slug === '') {
    ?>
    <div class="container py-5">
        <div class="row">
            <div class="text-center card">
                <h1 class="fw-bold">Bloomex Australia Blog: Flower Delivery Professionals</h1>
            </div>
        </div><br>


            <div class="row card-grid">
            <?php foreach ($blogPosts as $post):
                $title =  $post->title;
                $shortDesc =  $post->short_description;
                $slug = $post->slug;
                $url = "/blogs/$slug";
                $image = $post->thumb_link;
                $imageLink = $image ?: "$mosConfig_live_site/templates/bloomex_adaptive/images/no_image.webp";
                ?>
                <div class="col-md-4 col-sm-6">
                    <div class="card-item">
                        <a href="<?= $url ?>">
                            <img class="card-image" src="<?= $imageLink ?>" alt="Card image cap">
                        </a>
                        <div class="card-body">
                            <h3 class="card-title text-center"><?= html_entity_decode(mb_substr(strip_tags($title), 0, 50)) ?></h3>
                            <p class="card-text text-center text-muted">
                                    <?= html_entity_decode(mb_substr(strip_tags($shortDesc), 0, 130)) ?>...
                                    <a href="<?= $url ?>"><b>Read More</b></a>
                            </p>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
            </div>


        <?php if ($totalPages > 1):
            $urlPerPage = "blogs";
            ?>
            <div class="row mt-5">
                <div class="col text-center">
                    <nav>
                        <ul class="pagination justify-content-center">
                            <?php if ($page > 1): ?>
                                <li class="page-item">
                                    <a class="page-link" href="/<?= $urlPerPage ?>/perPage=<?= $page - 1 ?>">« Prev</a>
                                </li>
                            <?php endif; ?>

                            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                                    <a class="page-link" href="/<?= $urlPerPage ?>/perPage=<?= $i ?>"><?= $i ?></a>
                                </li>
                            <?php endfor; ?>

                            <?php if ($page < $totalPages): ?>
                                <li class="page-item">
                                    <a class="page-link" href="/<?= $urlPerPage ?>/perPage=<?= $page + 1 ?>">Next »</a>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </nav>
                </div>
            </div>
        <?php endif; ?>

    </div>

<?php } else {
    $post = $blogPosts[0];
    $title = $post->title;
    $description =  $post->description;
    $createdBy ='Bloomex Australia';
    $createdAt = date('F j, Y', strtotime($post->created_at));

    $where = '';
    $query = "SELECT id FROM jos_vm_blog_post_data WHERE slug ='" . $slug . "' AND post_id = " . (int)$post->id ;
    $database->setQuery($query);
    $data = false;
    $database->loadObject($data);
    
    if ($data) {
        $where = ' AND blog_post_data_id = ' . $data->id;
    }
    
    $queryImage = "SELECT image_link FROM jos_vm_blog_post_images WHERE post_id = " . (int)$post->id . $where;
    $database->setQuery($queryImage);
    $image = false;
    $database->loadObject($image);
    $imageLink = $image && $image->image_link ? $image->image_link : "$mosConfig_live_site/templates/bloomex_adaptive/images/no_image.webp";
    ?>
    <div class="container">
        <div class="row">
            <div class="card p-4 shadow-sm rounded-4">
                <div class="header-row">
                    <a href="/blogs">
                        <img src="<?php echo IMAGEURL ?>ps_image/undo.png" alt="Back" height="32" width="32">
                    </a>
                    <h2 class="mb-3" style="margin-left: 8px;"><?= html_entity_decode($title) ?></h2>
                </div>
                <hr>
                <div class="text-muted" style="margin: 8px 10px;">
                    <img style="width: 22px;margin:3px;margin-top: -5px;" src="https://secure.gravatar.com/avatar/29c209c993d4f1e1bebc8f3ea604b2444a7a046158a1eea1b3f48476745ac7d0?s=96&amp;d=mm&amp;r=g" alt="William Matthews">
                    <span style="font-size: 18px;"><?= html_entity_decode($createdBy) ?> · <?= $createdAt ?></span>
                </div>
                <div style="text-align: center">
                    <img src="<?= html_entity_decode($imageLink) ?>" alt="<?= html_entity_decode($title) ?>" style="margin: 5px;width: 80%;">
                </div>
                <div class="blog-description fs-5" style="margin: 15px 10px;">
                    <p style="font-size: 20px;"><?= $description ?></p>
                </div>
            </div>
        </div>
    </div>
<?php } ?>

<style>
    .card-item {
        height: 490px;
        padding: 5px;
    }
    .card {
        border-radius: 8px;
        padding: 20px;
        background: #fff;
    }
    .card-image {
        height: 320px;
        width: 100%;
        object-fit: cover;
    }
    .card-grid {
        grid-template-columns: repeat(3, 1fr);
        grid-auto-flow: column;
        border-radius: 8px;
        padding: 20px;
        background: #fff;
    }
    .pagination {
        font-size: 1.2rem;
    }
    .page-item.active .page-link {
        background-color: #007bff;
        border-color: #007bff;
        color: white;
    }
    .header-row {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .header-row h2 {
        margin: 0;
    }
</style>