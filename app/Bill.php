<?php

namespace App;

/**
 * CRUD for `property_bills`
 */
class Bill
{
    /**
     * Retrievs data from `property_bills`
     * 
     * @param int $propertyId
     * 
     * @return array $bills
     */
    public static function getPropertyBills(
        int $propertyId,
        \PDO $pdo
    ): array {
        $sql = 'SELECT `bill_title` FROM `bills_properties` `b_p`
                JOIN `bills` `b` ON `b_p`.`bill_id` = `b`.`bill_id`
                WHERE `property_id` = ?';

        $stmt = $pdo->prepare($sql);

        $stmt->execute([$propertyId]);

        $bills = [];
        while ($row = $stmt->fetch()) {
            $bills[] = $row['bill_title'];
        }

        return $bills;
    }

    /**
     * Inserts data into `property_bills`
     * 
     * @param int   $propertyId
     * @param array $bills
     * @param \PDO  $pdo - database connection
     * 
     * @return bool
     */
    public static function addBillsIncludedToProperty(
        int $propertyId,
        array $bills,
        \PDO $pdo
    ): bool {
        $valueSql = '';

        foreach ($bills as $key => $bill) {
            $valueSql .= "(:{$key}, {$propertyId}),";
        }

        $sql = 'INSERT INTO `bills_properties` VALUES '.substr($valueSql, 0, -1);

        $stmt = $pdo->prepare($sql);
        return $stmt->execute($bills);
    }

    /**
     * Deletes data from  `property_bills` table
     * 
     * @param int   $propertyId
     * @param \PDO  $pdo - database connection
     * 
     * @return bool
     */
    public static function removeBillsIncluded(int $propertyId, \PDO $pdo)
    {
        $sql = 'DELETE FROM `bills_properties` WHERE `property_id` = ?';

        return $pdo->prepare($sql)->execute([$propertyId]);
    }

    /**
     * Updates data in  `property_bills` table
     * 
     * @param int   $propertyId
     * @param array $bills
     * @param \PDO  $pdo - database connection
     * 
     * @return bool
     */
    public static function updateBillsIncluded(int $propertyId, $bills, \PDO $pdo)
    {
        if (!self::removeBillsIncluded($propertyId, $pdo)) {
            return [
                'success' => false,
                'errors'  => [
                    'Failed to delete old bills'
                ]
            ];
        }

        if (!self::addBillsIncludedToProperty($propertyId, $bills, $pdo)) {
            return [
                'success' => false,
                'errors'  => [
                    'Failed to insert new bills'
                ]
            ];
        }
    
        return [
            'success' => true
        ];
    }

    /**
     * Deletes data in `property_bills` table
     * 
     * @param int   $propertyId
     * @param \PDO  $pdo - database connection
     * 
     * @return bool
     */
    public static function removePropertyBills(int $propertyId, \PDO $pdo)
    {
        $sql = "DELETE FROM `bills_properties` WHERE `property_id` = ?";
        $stmt = $pdo->prepare($sql);

        return $stmt->execute([$propertyId]); 
    }
}
