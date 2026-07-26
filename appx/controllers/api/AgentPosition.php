<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';

use Restserver\Libraries\REST_Controller;

class AgentPosition extends REST_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Agent_position_model', 'agentModel');
    }

    /**
     * 🚑 L'agent envoie / met à jour sa position
     * POST : agent_id, latitude, longitude
     */
    public function updateAgentPosition_post()
    {
        if (
            !empty($this->input->post('agent_id')) &&
            !empty($this->input->post('latitude')) &&
            !empty($this->input->post('longitude'))
        ) {
            $agent_id = $this->input->post('agent_id');
            $lat      = $this->input->post('latitude');
            $lng      = $this->input->post('longitude');

            $save = $this->agentModel->save_position($agent_id, $lat, $lng);

            if ($save) {
                $response['code'] = 1;
                $response['data'] = '';
                $response['msg']  = "Position de l'agent mise à jour";
            } else {
                $response['code'] = 0;
                $response['data'] = '';
                $response['msg']  = "Erreur lors de l'enregistrement";
            }
        } else {
            $response['code'] = 0;
            $response['data'] = '';
            $response['msg']  = "Vérifier les variables envoyées";
        }

        return $this->response($response, REST_Controller::HTTP_OK);
    }

    /**
     * 👤 Le patient récupère la position de l'agent
     * POST : agent_id
     */
    public function getAgentPosition_post()
    {
        if (!empty($this->input->post('agent_id'))) {

            $agent_id = $this->input->post('agent_id');
            $position = $this->agentModel->get_position($agent_id);

            if (empty($position)) {
                $response['code'] = 0;
                $response['data'] = '';
                $response['msg']  = "Position de l'agent indisponible";
            } else {
                $response['code'] = 1;
                $response['data'] = [
                    'latitude'   => $position->latitude,
                    'longitude'  => $position->longitude,
                    'updated_at' => $position->updated_at
                ];
                $response['msg'] = "Position récupérée";
            }
        } else {
            $response['code'] = 0;
            $response['data'] = '';
            $response['msg']  = "Vérifier les variables envoyées";
        }

        return $this->response($response, REST_Controller::HTTP_OK);
    }
}
