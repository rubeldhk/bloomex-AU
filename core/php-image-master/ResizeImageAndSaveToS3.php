<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/configuration.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/core/aws/S3ClientAdapter.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/core/php-image-master/ImageResize.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/core/php-image-master/ImageConvertToJpeg.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/core/php-image-master/ImageConvertToWebp.php';
class ResizeImageAndSaveToS3
{

    public function resizeProductImageAndSave($data,$imagePath,$db)
    {
        global $mosConfig_aws_s3_bucket_public_url;

        $s3 = new S3ClientAdapter();
        $imageName = pathinfo($imagePath, PATHINFO_FILENAME);

        if (is_file($imagePath)) {

            $oldValue = false;
            $query = "SELECT * FROM jos_vm_product_s3_images WHERE product_id = ".$data->product_id;
            $db->setQuery($query);
            $db->loadObject($oldValue);

            $query = "DELETE FROM jos_vm_product_s3_images WHERE product_id = ".$data->product_id;

            $db->setQuery($query);
            if (!$db->query()) {
                echo $db->stderr();
            }


            $fullImageLinkWebp = $mediumImageLinkWebp = $smallImageLinkWebp = $fullImageLinkJpeg = $mediumImageLinkJpeg = $smallImageLinkJpeg = '';
            $imagePath = (new ImageConvertToWebp($imagePath))->convert();
            $image = new ImageResize($imagePath);

            $imageNameWebp = $imageName.'.webp';
            $imageNameJpeg = $imageName.'.jpeg';
            $TmpWebpImagePath = __DIR__.'/tmp_'.$imageNameWebp;
            $TmpJpegImagePath = __DIR__.'/tmp_'.$imageNameJpeg;

            if($oldValue && $oldValue->full_image_link_webp)
                $s3->delete($oldValue->full_image_link_webp);
//        $fullImage = $image->resizeToHeight(447);
//        $image->save('tmp_'.$imageNameWebp);
//        $fullImageLinkWebp = $s3->upload('tmp_'.$imageNameWebp, $imageNameWebp,'product/full_webp');
            $fullImageLinkWebp = $s3->upload($imagePath, $imageNameWebp,'product/full_webp');


            if($oldValue && $oldValue->medium_image_link_webp)
                $s3->delete($oldValue->medium_image_link_webp);
            $mediumImage = $image->resizeToHeight(262);
            $image->save($TmpWebpImagePath);
            $mediumImageLinkWebp = $s3->upload($TmpWebpImagePath, $imageNameWebp,'product/medium_webp');

            if($oldValue && $oldValue->small_image_link_webp)
                $s3->delete($oldValue->small_image_link_webp);
            $smallImage = $image->resizeToHeight(163);
            $image->save($TmpWebpImagePath);
            $smallImageLinkWebp = $s3->upload($TmpWebpImagePath, $imageNameWebp,'product/small_webp');
            if (file_exists($TmpWebpImagePath)) {
                unlink($TmpWebpImagePath);
            }
            $imageConvertor = new ImageConvertToJpeg($imagePath, $TmpJpegImagePath, 70);

            if($imageConvertor->convert()){

                $imageJpeg = new ImageResize($TmpJpegImagePath);

                if($oldValue && $oldValue->full_image_link_jpeg)
                    $s3->delete($oldValue->full_image_link_jpeg);
//            $fullImageJpeg = $imageJpeg->resizeToHeight(447);
//            $imageJpeg->save('tmp_'.$imageNameJpeg);
//            $fullImageLinkJpeg = $s3->upload('tmp_'.$imageNameJpeg, $imageNameJpeg,'product/full_jpeg');
                $fullImageLinkJpeg = $s3->upload($TmpJpegImagePath, $imageNameJpeg,'product/full_jpeg');

                if($oldValue && $oldValue->medium_image_link_jpeg)
                    $s3->delete($oldValue->medium_image_link_jpeg);
                $mediumImageJpeg = $imageJpeg->resizeToHeight(262);
                $imageJpeg->save($TmpJpegImagePath);
                $mediumImageLinkJpeg = $s3->upload($TmpJpegImagePath, $imageNameJpeg,'product/medium_jpeg');

                if($oldValue && $oldValue->small_image_link_jpeg)
                    $s3->delete($oldValue->small_image_link_jpeg);
                $smallImageJpeg = $imageJpeg->resizeToHeight(163);
                $imageJpeg->save($TmpJpegImagePath);
                $smallImageLinkJpeg = $s3->upload($TmpJpegImagePath, $imageNameJpeg,'product/small_jpeg');
                if (file_exists($TmpJpegImagePath)) {
                    unlink($TmpJpegImagePath);
                }
            };

            $query = "INSERT INTO jos_vm_product_s3_images (
                                          full_image_link_webp,
                                          medium_image_link_webp,
                                          small_image_link_webp,
                                          full_image_link_jpeg,
                                          medium_image_link_jpeg,
                                          small_image_link_jpeg,
                                          product_id
                                      )
              VALUES (
                      '" . $db->getEscaped(str_replace($mosConfig_aws_s3_bucket_public_url, "", $fullImageLinkWebp)) . "',
                      '" . $db->getEscaped(str_replace($mosConfig_aws_s3_bucket_public_url, "", $mediumImageLinkWebp)) . "',
                      '" . $db->getEscaped(str_replace($mosConfig_aws_s3_bucket_public_url, "", $smallImageLinkWebp)) . "',
                      '" . $db->getEscaped(str_replace($mosConfig_aws_s3_bucket_public_url, "", $fullImageLinkJpeg)) . "',
                      '" . $db->getEscaped(str_replace($mosConfig_aws_s3_bucket_public_url, "", $mediumImageLinkJpeg)) . "',
                      '" . $db->getEscaped(str_replace($mosConfig_aws_s3_bucket_public_url, "", $smallImageLinkJpeg)) . "',
                      '" . $db->getEscaped($data->product_id) . "'
                    )
                 ";

            $db->setQuery($query);
            if (!$db->query()) {
                echo $db->stderr();
            }

        }
    }
    public function resizeCategoryImageAndSave($data,$imagePath,$db)
    {
        global $mosConfig_aws_s3_bucket_public_url;

        $s3 = new S3ClientAdapter();
        $imageName = pathinfo($imagePath, PATHINFO_FILENAME);

        if (is_file($imagePath)) {

            $oldValue = false;
            $query = "SELECT * FROM jos_vm_category_s3_images WHERE category_id = ".$data->category_id;
            $db->setQuery($query);
            $db->loadObject($oldValue);


            $query = "DELETE FROM jos_vm_category_s3_images WHERE category_id = ".$data->category_id;
            $db->setQuery($query);
            if (!$db->query()) {
                echo $db->stderr();
            }


            $fullImageLinkWebp = $fullImageLinkJpeg = '';
            $imagePath = (new ImageConvertToWebp($imagePath))->convert();
            $image = new ImageResize($imagePath);

            $imageNameWebp = $imageName.'.webp';
            $imageNameJpeg = $imageName.'.jpeg';
            $TmpWebpImagePath = __DIR__.'/tmp_'.$imageNameWebp;
            $TmpJpegImagePath = __DIR__.'/tmp_'.$imageNameJpeg;

            if($oldValue && $oldValue->full_image_link_webp)
                $s3->delete($oldValue->full_image_link_webp);
            $image->resizeToHeight(200);
            $image->save($TmpWebpImagePath);
            $fullImageLinkWebp = $s3->upload($TmpWebpImagePath, $imageNameWebp,'category/webp');
            if (file_exists($TmpWebpImagePath)) {
                unlink($TmpWebpImagePath);
            }

            $imageConvertor = new ImageConvertToJpeg($imagePath, $TmpJpegImagePath, 70);

            if($imageConvertor->convert()){

                $imageJpeg = new ImageResize($TmpJpegImagePath);

                if($oldValue && $oldValue->full_image_link_jpeg)
                    $s3->delete($oldValue->full_image_link_jpeg);
                $imageJpeg->resizeToHeight(200);
                $imageJpeg->save($TmpJpegImagePath);
                $fullImageLinkJpeg = $s3->upload($TmpJpegImagePath, $imageNameJpeg,'category/jpeg');
                if (file_exists($TmpJpegImagePath)) {
                    unlink($TmpJpegImagePath);
                }

            };

            $query = "INSERT INTO jos_vm_category_s3_images (
                                          full_image_link_webp,
                                          full_image_link_jpeg,
                                          category_id
                                      )
              VALUES (
                      '" . $db->getEscaped(str_replace($mosConfig_aws_s3_bucket_public_url, "", $fullImageLinkWebp)) . "',
                      '" . $db->getEscaped(str_replace($mosConfig_aws_s3_bucket_public_url, "", $fullImageLinkJpeg)) . "',
                      '" . $db->getEscaped($data->category_id) . "'
                    )
                 ";

            $db->setQuery($query);
            if (!$db->query()) {
                echo $db->stderr();
            }

        }
    }

    public function resizeSliderImageAndSave($imagePath,$filename)
    {
        global $mosConfig_aws_s3_bucket_public_url;
        $s3 = new S3ClientAdapter();
        $imageName = pathinfo($filename, PATHINFO_FILENAME);

        if (is_file($imagePath)) {

            $fullImageLinkWebp = $fullImageLinkJpeg = '';
            $imagePath = (new ImageConvertToWebp($imagePath))->convert();
            $image = new ImageResize($imagePath);

            $imageNameWebp = $imageName.'.webp';
            $imageNameJpeg = $imageName.'.jpeg';
            $TmpWebpImagePath = __DIR__.'/tmp_'.$imageNameWebp;
            $TmpJpegImagePath = __DIR__.'/tmp_'.$imageNameJpeg;

            $image->resizeToHeight(282);
            $image->save($TmpWebpImagePath);
            $fullImageLinkWebp = $s3->upload($TmpWebpImagePath, $imageNameWebp,'slider/webp');
            if (file_exists($TmpWebpImagePath)) {
                unlink($TmpWebpImagePath);
            }

            $imageConvertor = new ImageConvertToJpeg($imagePath, $TmpJpegImagePath, 70);

            if($imageConvertor->convert()){

                $imageJpeg = new ImageResize($TmpJpegImagePath);

                $imageJpeg->resizeToHeight(200);
                $imageJpeg->save($TmpJpegImagePath);
                $fullImageLinkJpeg = $s3->upload($TmpJpegImagePath, $imageNameJpeg,'slider/jpeg');
                if (file_exists($TmpJpegImagePath)) {
                    unlink($TmpJpegImagePath);
                }

            };
            return  str_replace($mosConfig_aws_s3_bucket_public_url, "", $fullImageLinkWebp);
        }
        return false;
    }
    public function resizeHistoryImageAndSave($historyImageString)
    {
        $s3 = new S3ClientAdapter();

        $image = new ImageResize($historyImageString);

        $imageName = 'history_image_'.time().rand();
        $imageNameWebp = $imageName.'.webp';
        $TmpWebpImagePath = __DIR__.'/tmp_'.$imageNameWebp;

        $image->save($TmpWebpImagePath);
        $fullImageLinkWebp = $s3->upload($TmpWebpImagePath, $imageNameWebp,'history');
        if (file_exists($TmpWebpImagePath)) {
            unlink($TmpWebpImagePath);
        }
        return $fullImageLinkWebp;

    }
}