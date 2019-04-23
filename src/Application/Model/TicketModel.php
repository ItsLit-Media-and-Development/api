<?php
namespace API\Model;

use API\Library;


class TicketModel extends Library\BaseModel
{
	public function __construct()
	{
		parent::__construct();
    }
    
    public function create_ticket(array $data)
    {
        $stmt = $this->_db->prepare("INSERT INTO ticket (name, email, message, submitted_at) VALUES (:name, :email, :message, now())");
        $stmt->execute(
            [
                ':name'    => $data['name'],
                ':email'   => $data['email'],
                ':message' => $data['message']
            ]
        );
    }

    public function view_ticket($id)
    {
        $stmt = $this->_db->prepare("SELECT name, email, message, submitted_at FROM ticket WHERE id = :id");
        $stmt->execute([':id' => $id]);

        $this->_output = $stmt->fetch(\PDO::FETCH_ASSOC);

        return $this->_output;
    }
}