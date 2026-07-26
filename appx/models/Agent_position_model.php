<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Agent_position_model extends CI_Model
{
    protected $table = 'agents_positions';

    public function save_position($agent_id, $lat, $lng)
    {
        $exist = $this->db
            ->where('agent_id', $agent_id)
            ->get($this->table)
            ->row();

        $data = [
            'agent_id'   => $agent_id,
            'latitude'   => $lat,
            'longitude'  => $lng,
            'updated_at' => date('Y-m-d H:i:s')
        ];

        if ($exist) {
            $this->db->where('agent_id', $agent_id);
            return $this->db->update($this->table, $data);
        } else {
            return $this->db->insert($this->table, $data);
        }
    }

    public function get_position($agent_id)
    {
        return $this->db
            ->where('agent_id', $agent_id)
            ->get($this->table)
            ->row();
    }
}
