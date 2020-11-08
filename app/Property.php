<?php

namespace App;

/**
 * CRUD class for `properties` table.
 * Also has function to work with filter and property data.
 */
class Property
{
    /**
     * Retrieves tickets data from `properties` table.
     * 
     * @param array $filterData
     * @param int   $page - Page which user wants to see
     * @param int   $showPerPage - Value says how many tickets show per page
     * @param bool  $active - find only active properties or not
     * 
     * @return array $tickets
     */
    public static function getTicketsData(
        array $filterData,
        int   $page,
        int   $showPerPage,
        bool  $active = true
    ): array {
        $pdo = Component\DBConnection::getConnection();

        $filterQueryData = self::buildSqlQueryFromFilterData($filterData);

        $isActive = $active ? 'WHERE `is_active` = 1 ' : ' ';

        $sql = "SELECT `property_id`,
                       `p_t`.`type_title`,
                       `price_per_week`,
                       `first_address_line`,
                       `is_active`,
                       `post_code`,
                       `second_address_line`,
                       `condition`,
                       `available_bedrooms`,
                       `total_bedrooms`
                  FROM `properties` `p`
                  JOIN `property_types` `p_t`
                  ON `p`.`type_id` =  `p_t`.`type_id`".$isActive;

        $sql .= $filterQueryData['sql'];

        if ($showPerPage) {
            $sql .= ' '.'LIMIT :offset, :limit';
            $offset = ($page - 1) * $showPerPage;

            $data = array_merge(
                $filterQueryData['executeData'],
                [
                    "offset" => $offset,
                    "limit"  => $showPerPage
                ]
            );
        } else {
            $data = $filterQueryData['executeData'];
        }

        $stmt = $pdo->prepare($sql);
        $stmt->execute($data);

        $tickets = [];
        while ($row = $stmt->fetch()) {
            $tickets[] = $row;
        }

        return [
            'tickets'     => self::preparePropertyData($tickets, $pdo),
            'totalAmount' => self::getTicketsAmount($pdo, $filterQueryData)
        ];
    }

    /**
     * Tries to find property by given id
     * 
     * @param int   $propertyId
     * @param bool  $active - find only active property or not
     * 
     * @return array
     */
    public static function getPropertyById(int $propertyId, bool $active = false)
    {
        $pdo = Component\DBConnection::getConnection();

        $sql = "SELECT `property_id`,
                       `furnished`,
                       `p_t`.`type_title`,
                       `price_per_week`,
                       `first_address_line`,
                       `post_code`,
                       `second_address_line`,
                       `condition`,
                       `is_active`,
                       `available_bedrooms`,
                       `total_bedrooms`,
                       `deposit`,
                       `bathrooms_amount`,
                       `available_from`,
                       `comment`
                  FROM `properties` `p`
                  JOIN `property_types` `p_t`
                  ON `p`.`type_id` =  `p_t`.`type_id`
                  WHERE `property_id` = ?";

        $sql .= $active ? ' AND `is_active` = 1' : '';

        $stmt = $pdo->prepare($sql);

        $stmt->execute([$propertyId]);

        $property = $stmt->fetch();

        if ($property) {
            return self::preparePropertyData([$property], $pdo);
        } else {
            return null;
        }
    }

    /**
     * Gets ticket amount for particular filter
     * 
     * @param \PDO  $pdo - database connection
     * @param array $filterQueryData
     * 
     * @return int
     */
    public static function getTicketsAmount(\PDO $pdo, array $filterQueryData): int
    {
        $sql = "SELECT COUNT(`p`.`property_id`) AS `amount` FROM `properties` `p`
                WHERE `p`.`is_active` = 1 ";
        $sql .= $filterQueryData['sql'];

        $stmt = $pdo->prepare($sql);
        $stmt->execute($filterQueryData['executeData']);
        $row = $stmt->fetch();

        return $row['amount'];
    }

    /**
     * Formats proper array from given data.
     * 
     * @param array $properties
     * @param \PDO  $pdo - database connection
     * 
     * @return array
     */
    public static function preparePropertyData(
        array $properties,
        \PDO $pdo
    ): array {
        $newProperties = [];

        foreach ($properties as $property) {
            $tempArray = [];

            if (array_key_exists('furnished', $property)) {
                $tempArray['furnished'] = $property['furnished'] ? 'Furnished' : 'Unfurnished';
            }

            if (array_key_exists('date_available', $property)) {
                $tempArray['available_from'] = $property['available_from'];
            }

            if (array_key_exists('available_from', $property)) {
                $tempArray['available_from'] = $property['available_from'];
            }

            if (array_key_exists('bathrooms_amount', $property)) {
                $tempArray['bathrooms_amount'] = $property['bathrooms_amount'];
            }

            if (array_key_exists('comment', $property)) {
                $tempArray['comment'] = $property['comment'];
            }

            if (array_key_exists('is_active', $property)) {
                $tempArray['is_active'] = $property['is_active'] ? 'Active' : 'Not active';
            }

            if (array_key_exists('deposit', $property)) {
                $tempArray['deposit'] = $property['deposit'];

                $bills = Bill::getPropertyBills(
                    $property['property_id'],
                    $pdo
                );

                foreach ($bills as $key => $bill) {
                    $tempArray['rent'][$bill] = true;
                }

                if (count($bills)) {
                    $tempArray['bills_included'] = implode(", ", $bills);
                } else {
                    $tempArray['bills_included'] = 'Nothing';
                }

                $tempArray['first_address_line']  = $property['first_address_line'];
                $tempArray['second_address_line'] = $property['second_address_line'];
                $tempArray['post_code']           = $property['post_code'];
            }

            $tempArray['address'] = $property['first_address_line'];
            if ($property['second_address_line'] != '') {
                $tempArray['address'] .= ', '.$property['second_address_line'];
            }
            $tempArray['address'] .= ', '.$property['post_code'];

            $tempArray['type_title'] = $property['type_title'];
            $tempArray['price_per_week'] = $property['price_per_week'];

            $tempArray['condition'] = $property['condition'];

            $tempArray['property_id'] = $property['property_id'];

            $tempArray['images'] = Image::getPropertyImages(
                $property['property_id'],
                $pdo
            );

            $tempArray['available_bedrooms'] = $property['available_bedrooms'];
            $tempArray['total_bedrooms']     = $property['total_bedrooms'];

            $newProperties[] = $tempArray;
        }

        return $newProperties;
    }

    /**
     * Checks user input for any dangerours code
     * 
     * @param array $filterData
     * 
     * @return array $filterData - slighly amended "order-by" key
     */
    public static function validateFilterData(array $filterData)
    {
        if (!is_numeric($filterData['type']) ||
            !($filterData['type'] >= 0 && $filterData['type'] <= 4)
        ) {
            throw new \Exception("Filter data 'type' is wrong", 1);
            exit;
        }

        if (!is_numeric($filterData['location']) ||
            !($filterData['location'] >= 0 && $filterData['location'] <= 6)
        ) {
            throw new \Exception("Filter data 'location' is wrong", 1);
            exit;
        }

        if (!is_numeric($filterData['bedrooms']) ||
            !($filterData['bedrooms'] >= 0 && $filterData['bedrooms'] <= 4)
        ) {
            throw new \Exception("Filter data 'location' is wrong", 1);
            exit;
        }

        switch ($filterData['order-by']) {
            case 'newest':
                $filterData['order-by'] = 'ORDER BY `property_id` DESC';
                break;
            case 'oldest':
                $filterData['order-by'] = 'ORDER BY `property_id` ASC';
                break;
            case 'lowest-price':
                $filterData['order-by'] = 'ORDER BY `price_per_week` ASC';
                break;
            case 'highest-price':
                $filterData['order-by'] = 'ORDER BY `price_per_week` DESC';
                break;
            default:
                throw new \Exception("Filter data 'order-by' is wrong", 1);
                exit;
                break;
        }

        return $filterData;
    }

     /**
     * Checks property creation form fields for any errors
     * 
     * @param array $propertyData
     * @param array $images
     * 
     * @return array $errors
     */
    public static function validatePropertyData(array $propertyData, array $images): array
    {
        $errors = [];

        if ($propertyData['first_address_line'] === '') {
            $errors[] = '* 1 address line field cannot be empty';
        }

        if ($propertyData['post_code'] === '') {
            $errors[] = '* Post code field cannot be empty';
        }

        if ($propertyData['date_available'] === '') {
            $errors[] = '* Date available field cannot be empty';
        }
        if (!preg_match('~^[0-9]{4}-[0-9]{2}-[0-9]{2}$~', $propertyData['date_available'])) {
            $errors[] = '* The Date available field must have YYYY-MM-DD format';
        }

        if ($propertyData['total_bedrooms'] === '') {
            $errors[] = '* Total bedrooms field cannot be empty';
        }
        if (!is_numeric($propertyData['total_bedrooms'])) {
            $errors[] = '* Total bedrooms field amount must be a digit';
        }

        if ($propertyData['available_bedrooms'] === '') {
            $errors[] = '* Available bedrooms field cannot be empty';
        }
        if (!is_numeric($propertyData['available_bedrooms'])) {
            $errors[] = '* Available bedrooms field amount must be a digit';
        }

        if ($propertyData['bathrooms'] === '') {
            $errors[] = '* Bathrooms field cannot be empty';
        }
        if (!is_numeric($propertyData['bathrooms'])) {
            $errors[] = '* Bathrooms field amount must be a digit';
        }

        if ($propertyData['deposit'] === '') {
            $errors[] = '* Deposit field cannot be empty';
        }
        if (!is_numeric($propertyData['deposit'])) {
            $errors[] = '* Deposit must be a digit';
        }

        if ($propertyData['price_per_week'] === '') {
            $errors[] = '* Price per week field cannot be empty';
        }

        if (!is_numeric($propertyData['price_per_week'])) {
            $errors[] = '* Price must be a digit';
        }

        $allowedMimeTypes = [
            'image/png',
            'image/jpg',
            'image/jpeg'
        ];

        foreach ($images as $key => $image) {
            if ($propertyData['images'][$key]['title'] == '') {
                $errors[] = "Image Nr. #$key title field cannot be empty";
            }

            if (substr($key, 0, 3) === 'new') {
                if ($image !== NULL) {
                    if (!in_array($image->getClientMimeType(), $allowedMimeTypes)) {
                        $errors[] = "Image Nr. #$key has wrong image type.
                                     only PNG, JPG, JPEG, WEBP are allowed";
                    }
                } else {
                    $errors[] = "Image $key must have a image file";
                }
            }
        }

        return $errors;
    }

     /**
     * Builds SQL query using filter data
     * 
     * @param array $filterData
     * 
     * @return array - Array with execiution data and SQL query
     */
    public static function buildSqlQueryFromFilterData(
        array $filterData
    ): array {
        $filterData = self::validateFilterData($filterData);
        $executeData = [];
        $sql = '';

        if ($filterData['type'] != 0) {
            $sql .= 'AND `p`.`type_id` = :type ';
            $executeData['type'] = (int) $filterData['type'];
        }

        if ($filterData['location'] != 0) {
            $sql .= "AND `post_code` LIKE :location ";
            $executeData['location'] = "NN{$filterData['location']}%";
        }

        if ($filterData['bedrooms'] != 0) {
            $sql .= 'AND `available_bedrooms` = :bedrooms ';
            $executeData['bedrooms'] = (int) $filterData['bedrooms'];
        }

        $sql .= $filterData['order-by'];

        return [
            'sql'         => $sql,
            'executeData' => $executeData
        ];
    }

    /**
     * Adds data in `properties` table
     * 
     * @param array $propertyData
     * @param array $imagesFiles
     * 
     * @return array - array with success status and message
    */
    public static function createProperty(
        array $propertyData,
        array $imageFiles
    ) {
        $errors = self::validatePropertyData($propertyData, $imageFiles);

        if (count($errors) !== 0) {
            return [
                'success'     => false,
                'errors'      => $errors,
                'flushedData' => $propertyData
            ];
        }

        $pdo = Component\DBConnection::getConnection();

        $sql = 'INSERT INTO `properties` VALUES (
                    null,
                    :typeId,
                    :furnishing,
                    :firstAddressLine,
                    :postCode,
                    :secondAddressLine,
                    :pricePerWeek,
                    :deposit,
                    :bathroomsAmount,
                    :isActive,
                    :condition,
                    :availableBedrooms,
                    :totalBedrooms,
                    :availableFrom,
                    :comment
                )';

        $stmt = $pdo->prepare($sql);
        $result = $stmt->execute([
            'typeId'            => $propertyData['type'],
            'furnishing'        => $propertyData['furnishing'],
            'firstAddressLine'  => trim($propertyData['first_address_line']),
            'secondAddressLine' => trim($propertyData['second_address_line']),
            'postCode'          => trim($propertyData['post_code']),
            'condition'         => $propertyData['condition'],
            'availableFrom'     => $propertyData['date_available'],
            'totalBedrooms'     => $propertyData['total_bedrooms'],
            'availableBedrooms' => $propertyData['available_bedrooms'],
            'bathroomsAmount'   => $propertyData['bathrooms'],
            'deposit'           => $propertyData['deposit'],
            'comment'           => trim($propertyData['comment']),
            'isActive'          => $propertyData['active'],
            'pricePerWeek'      => trim($propertyData['price_per_week'])
        ]);

        if (!$result) {
            return [
                'success' => false,
                'errors' => [
                    'There was a problem inserting data into the databse'
                ]
            ];
        }

        $propertyId = $pdo->lastInsertId();

        $images = [];
        foreach ($propertyData['images'] as $key => $value) {
            $images[$key] = $value;
            $images[$key]['file']  = $imageFiles[$key];
        }

        $imagesAdded = Image::addImagesToProperty(
            $propertyId,
            $images,
            $pdo
        );

        if (!$imagesAdded['success']) {
            return $imagesAdded;
        }


        if (array_key_exists('rent', $propertyData)) {
            $billsAdded = Bill::addBillsIncludedToProperty(
                $propertyId,
                $propertyData['rent'],
                $pdo
            );

            if (!$billsAdded) {
                return [
                    'success' => false,
                    'errors' => [
                        'There was a problem inserting bills data into the database'
                    ]
                ];
            }
        }

        return [
            'success' => true
        ];
    }

    /**
     * updates data in `properties` table
     * 
     * @param array $propertyId - which should be updated
     * @param array $propertyData - new property data
     * @param array $imagesFiles - new or updated images
     * 
     * @return array - array with success status
    */
    public static function updateProperty(
        int $propertyId,
        array $propertyData,
        array $imageFiles
    ) {
        $errors = self::validatePropertyData($propertyData, $imageFiles);

        if (count($errors) !== 0) {
            return [
                'success'     => false,
                'errors'      => $errors
            ];
        }

        $pdo = Component\DBConnection::getConnection();

        $sql = 'UPDATE `properties` SET
                    `type_id` = :typeId,
                    `furnished` = :furnishing,
                    `first_address_line` = :firstAddressLine,
                    `post_code` = :postCode,
                    `second_address_line` = :secondAddressLine,
                    `price_per_week` = :pricePerWeek,
                    `deposit` = :deposit,
                    `bathrooms_amount` = :bathroomsAmount,
                    `is_active` = :isActive,
                    `condition` = :condition,
                    `available_bedrooms` = :availableBedrooms,
                    `total_bedrooms` = :totalBedrooms,
                    `available_from` = :availableFrom,
                    `comment` = :comment
                WHERE `property_id` = :propertyId';

        $stmt = $pdo->prepare($sql);
  
        $propertyUpdated = $stmt->execute([
            'typeId'            => $propertyData['type'],
            'furnishing'        => $propertyData['furnishing'],
            'firstAddressLine'  => trim($propertyData['first_address_line']),
            'secondAddressLine' => trim($propertyData['second_address_line']),
            'postCode'          => trim($propertyData['post_code']),
            'condition'         => $propertyData['condition'],
            'availableFrom'     => $propertyData['date_available'],
            'totalBedrooms'     => $propertyData['total_bedrooms'],
            'availableBedrooms' => $propertyData['available_bedrooms'],
            'bathroomsAmount'   => $propertyData['bathrooms'],
            'deposit'           => $propertyData['deposit'],
            'comment'           => trim($propertyData['comment']),
            'isActive'          => $propertyData['active'],
            'pricePerWeek'      => trim($propertyData['price_per_week']),
            'propertyId'        => $propertyId
        ]);

        if (!$propertyUpdated) {
            return [
                'success' => false,
                'errors'  => [
                    'There was a problem updating the database table'
                ]
            ];
        }

        $images = [];
        foreach ($propertyData['images'] as $key => $value) {
            $images[$key] = $value;
            $images[$key]['file']  = $imageFiles[$key];
        }

        $imagesUpdated = Image::updatePropertyImages($propertyId, $images, $pdo);

        if (!$imagesUpdated['success']) {
            return $imagesUpdated;
        }

        if (array_key_exists('rent', $propertyData)) {
            $billsUpdated = Bill::updateBillsIncluded($propertyId, $propertyData['rent'], $pdo);
            if (!$billsUpdated['success']) {
                return $billsUpdated;
            }
        }

        return [
            'success' => true
        ];
    }

    /**
     * Removes data from `properties` table by id
     * 
     * @param $propertyId - which property should be deleted
     * 
     * @return bool
     */
    public static function deleteProperty(int $propertyId)
    {
        $pdo = Component\DBConnection::getConnection();
        
        if (!Bill::removePropertyBills($propertyId, $pdo)) {
            return [
                'success' => false,
                'errors'  => [
                    'Failed to remove bills'
                ]
            ];
        }

        if (!Image::removePropertyImages($propertyId, $pdo)) {
            return [
                'success' => false,
                'errors'  => [
                    'Failed to remove images'
                ]
            ];
        }

        $sql = "DELETE FROM `properties` WHERE property_id = ?";
        $stmt = $pdo->prepare($sql);
        return $stmt->execute([$propertyId]);
    }

    /**
     * Gets all data from `property_types` table
     * 
     * @return array
     */
    public static function getPropertytypes()
    {
        $pdo = Component\DBConnection::getConnection();

        $sql = "SELECT `type_id`, `type_title` FROM  `property_types`";
        $types = [];
    
        foreach ($pdo->query($sql) as $type) {
            $types[] = $type;
        }
    
        return $types;
    }
}
