<?php
/**
* Тестовое задание - класс.
* 
* Написать класс init, от которого нельзя сделать наследника.
* 
* @author Anton Luzhbin <antonluzhbin@yandex.ru>
* @version 1.0
*/

namespace Application\Controller;

use \Zend\Db\Adapter\Adapter;
use \Zend\Db\ResultSet\ResultSet;

/**
 * Тестовый класс.
 */
final class init
{
    /**
     * Адаптер для подключения к БД.
     * 
     * @access private
     * @var \Zend\Db\Adapter\Adapter 
     */
    private $adapter;
    private $set = Array('normal', 'illegal', 'failed', 'success');
    
    /**
     * Конструктор класса.
     * 
     * Создает подключение к БД. 
     * Создает тестовую таблицу и заполняет ее данными.
     * 
     * @access public
     * @uses $adapter Для сохранения подключения к БД.
     * @return void
     */
    function __construct()
    {
        try 
        {
            $this->adapter = new \Zend\Db\Adapter\Adapter(array(
                'driver' => 'Mysqli',
                'database' => 'test',
                'username' => 'root',
                'password' => '1'
                ));
        } catch (Exception $ex) { }
        
        $this->create();
        $this->fill();
    }
    
    /**
     * Создает тестовую таблицу.
     * 
     * @access private
     * @uses $adapter Для выполнения запроса
     * @return void
     */
    private function create() 
    {
        $sql = "CREATE TABLE IF NOT EXISTS `test` (" .
            "`id` int(11) NOT NULL AUTO_INCREMENT," .
            "`script_name` varchar(25) NOT NULL," .
            "`start_time` int(11) NOT NULL," .
            "`end_time` int(11) NOT NULL," .
            "`result` set('normal','illegal','failed','success') NOT NULL," .
            "PRIMARY KEY (`id`)" .
            ") ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;";
        
        try 
        {
            $this->adapter->query($sql, Adapter::QUERY_MODE_EXECUTE);
        } catch (Exception $ex) { }
    } 
    
    /**
     * Заполняет тестовую таблицу данными.
     * 
     * @access private
     * @uses $adapter Для выполнения запроса
     * @return void
     */
    private function fill() 
    {
        for ($i = 0; $i < 100; ++$i)
        {
            $script_name = 'script_' + $i;
            $start_time = $i;
            $end_time = $i * 2;
            $result = $this->set[$i % 4];
            
            $sql = "INSERT INTO test (id, script_name, start_time, end_time, result) VALUES ('', " .
                "'" . $script_name . "', '" . $start_time . "', '" . $end_time . "', '" . $result . "')";

            try 
            {
                $this->adapter->query($sql, Adapter::QUERY_MODE_EXECUTE);
            } catch (Exception $ex) { }
        }
    } 
    
    /**
     * Выборка данных из тестовой таблицы.
     * 
     * @param string Значение для выборки по столбцу 'result': 'normal' или 'success'
     * @access public
     * @uses $adapter Для выполнения запроса
     * @return Array()
     */
    public function get($result) 
    {
        if ($result === $this->set[0] || $result === $this->set[3])
        {
            $sql = "SELECT * FROM test WHERE result = '" . $result . "'";
            
            try 
            {
                $res = $this->adapter->query($sql, Adapter::QUERY_MODE_EXECUTE);

                $resultSet = new \Zend\Db\ResultSet\ResultSet();
                $resultSet->initialize($res);

                return $resultSet->toArray();
                
            } catch (Exception $ex) 
            { 
                return Array();
            }
        }
        else 
        {
            throw new \Exception("Parameter 'result' can be 'normal' or 'success'");
        }
    } 
}
