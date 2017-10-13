<?php

namespace IrfanTOOR\Storage;

use Exception;
use SQLite3;

class SqliteStorage extends AbstractStorage
{
	protected
		$db;

	# Constructs the container
	#
	# $init   : array of data to be initialized
	# $adapter: A IrfanTOOR\Container\Adapter for storing container
	# $flags  : Container::LOCKED or Container::NOCASE, default is none of these
	function __construct($connection)
	{
		parent::__construct($connection);
	}
	
	function connect($connection) {
		$this->connection = $this->parse_connection($connection);
		
		if (!isset($this->connection['file']))
			throw new Exception('file not defined in the connection');
			
		$file = $this->connection['file'];
		
		if (!file_exists($file)) {
			$this->db = new SQLite3($file, SQLITE3_OPEN_READWRITE | SQLITE3_OPEN_CREATE);
			$this->db->query('CREATE TABLE "data" ("uid" integer not null primary key autoincrement, "nid" varchar not null, "id" varchar no null, "value" varchar)');
			$this->db->query('CREATE UNIQUE INDEX "data_id_unique" on "data" ("id")');
			$this->db->query('CREATE INDEX "data_nid_index" on "data" ("nid")');
		}
		else {
			$this->db = new SQLite3($file);
		}
		
		# todo -- manage to check locked
		$this->locked = 0;
		
		# todo -- manage to check nocase
		$this->nocase = 0;
	}
	
	# Sets an $identifier and its Value pair
	function set($id, $value = null)
	{
		if ($this->locked)
			return;
			
		if (is_array($id)) {
			foreach ($id as $k => $v) {
				$this->set($k, $v);
			}
		}
		else {
			if (is_string($id)) {
				$this->remove($id);
				
				$nid = strtolower($id);
					
				# $this->data[$id] = $value;
				$q = $this->db->prepare("INSERT INTO data (nid, id, value) VALUES (:nid, :id, :value)");
				$q->bindValue(':nid',   $nid);
				$q->bindValue(':id',    $id);
				$q->bindValue(':value', json_encode($value));
				$q->execute();
			}
		}
	}
	
	# Checks if the identifier is present in the container
	function has($id) 
	{
		if ($this->nocase) {
			$q = $this->db->prepare("SELECT count(*) FROM data WHERE nid=:id");
			$q->bindValue(':id',   strtolower($id));
		}
		else {
			$q = $this->db->prepare("SELECT count(*) FROM data WHERE id=:id");
			$q->bindValue(':id',   $id);
		}
		
		$result = $q->execute();
		$row = $result->fetchArray();
		return ($row['0']) ? true : false;
	}
	
	# Gets the value associated with an identifier
	function get($id, $default = null) 
	{
		if ($this->nocase) {
			$q = $this->db->prepare("SELECT * FROM data WHERE nid=:id limit 1");
			$q->bindValue(':id',   strtolower($id));
		}
		else {
			$q = $this->db->prepare("SELECT * FROM data WHERE id=:id limit 1");
			$q->bindValue(':id',   $id);
		}
		
		$result = $q->execute();
		$row = $result->fetchArray();
		if ($row)
			return json_decode($row['value'],1);
		else
			return $default;
	}
	
	# Removes the value from identified by an identifier from the container
	function remove($id) 
	{
		if ($this->locked)
			return false;
		
		if ($this->nocase) {
			$q = $this->db->prepare("DELETE FROM data WHERE nid=:id");
			$q->bindValue(':id',   strtolower($id));
		}
		else {
			$q = $this->db->prepare("DELETE FROM data WHERE id=:id");
			$q->bindValue(':id',   $id);
		}
		
		$q->execute();
	}
	
	# Clears the container by removing all of the contained values
	function clear()
	{
		if ($this->locked)
			return false;
		
		$this->db->query("DELETE FROM data");
	}
	
	# Returns the container as an array collection
	function toArray($limit = 100)
	{
		$q = $this->db->prepare("SELECT * FROM data LIMIT $limit");
		$result = $q->execute();
		$array = [];
		while ($row = $result->fetchArray()) {
			$array[$row[2]] = json_decode($row[3],1);
		}		
		return $array;
	}
}
