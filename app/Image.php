<?php

namespace App;

use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\Exception\FileNotFoundException;

/**
 * CRUD class for `images` table.
 * Uploads, removes image files.
 */
class Image
{
    /**
     * Retrievs data from `images` table by `property_id` column
     * 
     * @param int  $propertyId
     * @param \PDO $pdo - database connection
     * 
     * @return array $images
     */
    public static function getPropertyImages(
        int $propertyId,
        \PDO $pdo
    ): array {
        $sql = 'SELECT `image_id`, `image_title`, `image_path` FROM `images` WHERE `property_id` = ?';

        $stmt = $pdo->prepare($sql);

        $stmt->execute([$propertyId]);

        $images = [];
        while ($row = $stmt->fetch()) {
            $images[] = $row;
        }

        return $images;
    }

    /**
     * Retrievs data from `images` table by `image_id` column
     * 
     * @param int  $imageId
     * @param \PDO $pdo - database connection
     * 
     * @return array - image data
     */
    public static function getImageById(
        int $imageId,
        \PDO $pdo
    ): array {
        $sql = 'SELECT `image_id`, `image_title`, `image_path` FROM `images` WHERE `image_id` = ?';
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$imageId]);

        return $stmt->fetch();
    }

    /**
     * Tries to update data in `images` table by `property_id` column
     * 
     * @param int  $propertyId
     * @param array $images - form data
     * @param \PDO $pdo - database connection
     * 
     * @return array - success or error with message
     */
    public static function updatePropertyImages(
        int   $propertyId,
        array $images,
        \PDO  $pdo
    ) {
        $newImages            = [];
        $oldImagesWithoutFile = [];
        $oldImagesWithFile    = [];
        $imagesUpdated        = false;

        foreach ($images as $key => $image) {
            if (substr($key, 0, 3) === 'new') {
                $newImages[] = $image;
            } else {
                if ($image['file'] === NULL) {
                    $oldImagesWithoutFile[] = $image;
                } else {
                    $oldImagesWithFile[] = $image;
                }
            }
        }

        if (count($newImages) !== 0) {
            $imagesUpdated = self::addImagesToProperty($propertyId, $newImages, $pdo);
            if (!$imagesUpdated) {
                return [
                    'success' => false,
                    'errors'  => [
                        'Could not update new images'
                    ]
                ];
            }
        }

        if (count($oldImagesWithoutFile) !== 0) {
            $imagesUpdated = self::updateImagesTitle($oldImagesWithoutFile, $pdo);
            if (!$imagesUpdated) {
                return [
                    'success' => false,
                    'errors'  => [
                        'Could not update old images title'
                    ]
                ];
            }
        }

        if (count($oldImagesWithFile) !== 0) {
            $imagesUpdated = self::updateImagesWithFile($oldImagesWithFile, $pdo);
            if (!$imagesUpdated) {
                return [
                    'success' => false,
                    'errors'  => [
                        'Could not update old images title and file'
                    ]
                ];
            }
        }

        return [
            'success' => true
        ];

    }

    /**
     * Updates `image_title` data in `images` table
     * 
     * @param array $images
     * @param \PDO  $pdo - database connection
     * 
     * @return bool - if there will not be any error it will return true 
     */
    public static function updateImagesTitle(
        array $images,
        \PDO $pdo
    ) {
        $stmt = $pdo->prepare("UPDATE `images` SET `image_title` = ? WHERE `image_id` = ?");
        foreach ($images as $image)
        {
            $stmt->execute([$image['title'], $image['id']]);
        }

        return true;
    }

    /**
     * Updates `image_title` and `image_path` data in `images` table.
     * Removes old image file.
     * 
     * @param array $images
     * @param \PDO  $pdo - database connection
     * 
     * @return bool - if there will not be any error it will return true 
     */
    public static function updateImagesWithFile(
        array $images,
        \PDO $pdo
    ) {
        foreach ($images as $image) {
            
            $oldFilepath = realpath(PUBLIC_PATH.$images[0]['old_filepath']);
            if (file_exists($oldFilepath)) {
                unlink($oldFilepath);
            }

            $newFilepath = self::uploadImage($image);

            $stmt = $pdo->prepare("UPDATE `images` SET `image_title` = ?, `image_path` = ? WHERE `image_id` = ?");
            foreach ($images as $image)
            {
                $stmt->execute([$image['title'], $newFilepath, $image['id']]);
            }
        }

        return true;
    }

    /**
     * Inserts data into `images` table.
     * 
     * @param int   $propertyId
     * @param array $images
     * @param \PDO  $pdo - database connection
     * 
     * @return array - array with success status and message
     */
    public static function addImagesToProperty(
        int   $propertyId,
        array $images,
        \PDO  $pdo
    ): array {
        $insertValues = '';
        $executeData  = [];

        $key = 0;
        foreach ($images as $image) {
            $key++;
            $filePath = self::uploadImage($image);

            $insertValues .= "(:title{$key}, {$propertyId}, :file{$key}),";

            $executeData['title'.$key] = $image['title'];
            $executeData['file'.$key]  = $filePath;
        }

        $sql = 'INSERT INTO `images` (`image_title`, `property_id`, `image_path`)
                VALUES '.substr($insertValues, 0, -1);

        $stmt = $pdo->prepare($sql);
        if ($stmt->execute($executeData)) {
            return [
                'success' => true
            ];
        } else {
            return [
                'success' => false,
                'errors' => [
                    'Image uploading error: database insertion error'
                ]
            ];
        }
    }

    /**
     * Uploads image to the server
     * 
     * @param $images
     * 
     * @return array - array with success status and message 
     */
    public static function uploadImage(array $image): string
    {
        do {
            $fileName = uniqid();
            $fileName .= '.'.strtolower($image['file']->getClientOriginalExtension());
        } while (file_exists(PROPERTY_IMAGES_PATH.'/'.$fileName));

        $filePath = PROPERTY_IMAGES_WEB_PATH.'/'.$fileName;

        try {
            $image['file']->move(PROPERTY_IMAGES_PATH, $fileName);
        } catch (FileException $e) {
            return [
                'success' => false,
                'errors' => [
                    'Image uploading error: '.$e->getErrorMessage()
                ]
            ];
        }

        return $filePath;
    }

    /**
     * Deletes data from `image` table and image files from the server
     * 
     * @param $propertyId - for which property images should be removed
     * @param $pdo        - database connection
     * 
     * @return bool
     */
    public static function removePropertyImages(int $propertyId, \PDO $pdo)
    {
        $images = self::getPropertyImages($propertyId, $pdo);
        self::removeImageFiles($images);

        $sql = "DELETE FROM `images` WHERE `property_id` = ?";
        $stmt = $pdo->prepare($sql);

        return $stmt->execute([$propertyId]);
    }

    /**
     * Deletes data from `image` table and image files from the server
     * 
     * @param $imageId
     * 
     * @return array bool
     */
    public static function removeImageById(int $imageId)
    {
        $pdo = Component\DBConnection::getConnection();

        $image = self::getImageById($imageId, $pdo);
        self::removeImageFiles($image);

        $sql = "DELETE FROM `images` WHERE `image_id` = ?";
        $stmt = $pdo->prepare($sql);

        return $stmt->execute([$imageId]);
    }

    /**
     * Removes image files from the server
     * 
     * @param array $images
     */
    public static function removeImageFiles(array $images)
    {
        foreach ($images as $image) {
            $filepath = realpath(PUBLIC_PATH.$image['image_path']);
            if (file_exists($filepath)) {
                unlink($filepath);
            }
        }
    }
}
