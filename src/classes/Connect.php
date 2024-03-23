<?php
/**
* Class for all the database operations.
*/

namespace App\Classes;

use Dotenv\Dotenv;
use Exception;
use PDO;
use PDOException;

class Connect
{
    private float $soilPrice;
    private int   $vatRate;
    private PDO   $connection;

    /**
     * Initializes a new instance of the Connect class.
     * @throws Exception
     */
    public function __construct()
    {
        try {
            self::loadConfig();
            $settings = $_ENV;
            $connection = new PDO(
                $settings['DB_DRIVER'] . ":host=" . $settings['DB_HOST'] .
                ";dbname=" . $settings['DB_SCHEMA'] . ";charset=" .
                $settings['DB_CHARSET'] . ";port=" .
                $settings['DB_PORT'], $settings['DB_USER'], $settings['DB_PASSWORD']);
            $this->soilPrice = $settings['SOIL_PRICE'] ?? 0;
            $this->vatRate = $settings['VAT_PRICE'] ?? 0;
            $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->connection = $connection;
        } catch (PDOException $e) {
            throw new PDOException('Error: ' . $e->getMessage());
        } catch (Exception $e) {
            throw new Exception('Error: ' . $e->getMessage());
        }
    }

    /**
     * Loads config from .env file.
     */
    private static function loadConfig()
    {
        $env = Dotenv::createImmutable(__DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..');
        $env->load();
    }

    /**
     * Returns default vat rate.
     *
     * @return int
     */
    public function getVatRate(): int
    {
        return $this->vatRate;
    }

    /**
     * Returns default soil price.
     *
     * @return float
     */
    public function getSoilPrice(): float
    {
        return $this->soilPrice;
    }

    /**
     * Returns PDO connection property of object.
     *
     * @return PDO
     */
    public function getConnection(): PDO
    {
        return $this->connection;
    }

    /**
     * Executes a prepared SQL query with parameters.
     *
     * @param string $sql
     * @param array  $params
     * @param bool   $returnBool
     * @param bool   $updated
     *
     * @return array|bool|string
     *
     * @throws PDOException
     */
    public function buildQuery(string $sql, array $params = [], bool $returnBool = false, bool $updated = false)
    {
        try {
            $results = [];
            $query = $this->getConnection()->prepare($sql);

            if ($query) {
                if (count($params)) {
                    foreach ($params as $key => $param) {
                        $query->bindValue($key, $param);
                    }
                }

                $query->execute();

                if ($returnBool) {
                    if ($updated) {
                        return $query->rowCount() > 0;
                    }

                    return $this->getConnection()->lastInsertId();
                }

                $results = $query->fetchAll(PDO::FETCH_ASSOC);
            }
        } catch (PDOException $e) {
            throw new PDOException('Error: ' . $e->getMessage());
        }

        return $results;
    }

    /**
     * Finds one record in DB with specific values.
     *
     * @param array  $params
     * @param string $table
     * @param string $selectField
     *
     * @return array|null
     *
     * @throws PDOException
     */
    public function findOneBy(array $params, string $table, string $selectField): ?array
    {
        if (empty($params)) {
            return null;
        }

        $where = '';

        foreach ($params as $key => $param) {
            $where .= (empty($where) ? ' WHERE ' : ' AND ') .
                $key . ' = :' . $key;
        }

        return $this->buildQuery(
            'SELECT ' . htmlspecialchars($selectField) . ' ' .
            'FROM ' . htmlspecialchars($table) .
            $where .
            ' LIMIT 1',
            $params
        );
    }

    /**
     * Inserts the record into DB.
     *
     * @param array  $params
     * @param string $table
     *
     * @return bool|string
     */
    public function insert(array $params, string $table)
    {
        $cols = '';
        $vals = '';

        foreach ($params as $key => $value) {
            $cols .= (!empty($cols) ? ', ' : '') . $key;
            $vals .= (!empty($vals) ? ', ' : '') . ':' . $key;
        }

        $qry = 'INSERT INTO ' . htmlspecialchars($table).
        ' (' . $cols . ') VALUES (' . $vals . ')';

        return $this->buildQuery($qry, $params, true);
    }

    /**
     * Updates the record in the database.
     *
     * @param array  $params
     * @param string $table
     * @param array  $recordIdentifiers
     *
     * @return bool|string
     */
    public function update(array $params, string $table, array $recordIdentifiers)
    {
        $set = '';
        $where = '';

        foreach ($params as $key => $value) {
            $set .= (!empty($set) ? ', ' : '') . $key . ' = :' . $key;
        }

        foreach ($recordIdentifiers as $iKey => $identifier) {
            $where .= (!empty($where) ? ' AND ' : '') . $iKey . ' = :' . $iKey;
            $params[$iKey] = $identifier;
        }

        $qry = 'UPDATE ' . htmlspecialchars($table) .
            ' SET ' . $set . ' WHERE ' .
            $where;

        return $this->buildQuery($qry, $params, true, true);
    }

    /**
     * Removes one record from DB with specific values.
     *
     * @param array  $params
     * @param string $table
     *
     * @return bool
     */
    public function remove(array $params, string $table): bool
    {
        if (empty($params)) {
            return false;
        }

        $where = '';

        foreach ($params as $key => $param) {
            $where .= (empty($where) ? ' WHERE ' : ' AND ') .
                $key . ' = :' . $key;
        }

        return !(false === $this->buildQuery(
                'DELETE FROM ' . htmlspecialchars($table) .
                $where,
                $params,
                true
            ));
    }

    /**
     * Finds all records in the specific table.
     *
     * @param string $table
     *
     * @return array
     */
    public function findAll(string $table): array
    {
        return $this->buildQuery('SELECT * FROM ' . htmlspecialchars($table));
    }
}