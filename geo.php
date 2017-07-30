<?php
class Geo
{
    protected $_config;

    public function configure($options=array())
    {
        $this->_config = $options;
    }

    public function geocode($address='')
    {
        $params = [
            'address'=>$address,
            'key'=>$this->_config['apikey']
        ];
        $url = 'https://maps.googleapis.com/maps/api/geocode/json?' . http_build_query($params);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        $result = json_decode(curl_exec($ch), true);
        curl_close($ch);
        $geo = [
            'latitude'=>0.0,
            'longitude'=>0.0
        ];

        if ( isset($result['results'][0]['geometry']) ){
            $geo = [
                'latitude'=>$result['results'][0]['geometry']['location']['lat'],
                'longitude'=>$result['results'][0]['geometry']['location']['lng']
            ];
        }
        return $geo;
    }

    /**
     * Calculate distance between two addresses and returns distance in miles.
     * @param  string $from     Address Origin
     * @param  string $to       Address Destination
     * @param  string $apikey   API Key
     * @return float            E.g. 1400.456
     */
    public function miles($from='', $to='')
    {
        $from_geo = $this->geocode($from);
        $to_geo = $this->geocode($to);
        return $this->distance($from_geo, $to_geo);
    }

    /**
     * Calculate distance in miles using latitude and longitude
     * @param  array  $from_geo  From, an array consisting of latitude and longitude
     * @param  array  $to_geo    To, an array consisting of latitude and longitude
     * @return float             E.g. 1400.456
     */
    public function distance($from_geo = array(), $to_geo = array())
    {
        if( ($from_geo['latitude']===0.0 && $from_geo['longitude']===0.0) ||
            ($to_geo['latitude']===0.0 && $to_geo['longitude']===0.0) ){
            return 0.0;
        }

        $theta = $from_geo['longitude'] - $to_geo['longitude'];
        $distance = sin(deg2rad($from_geo['latitude'])) * sin(deg2rad($to_geo['latitude'])) +
            cos(deg2rad($from_geo['latitude'])) * cos(deg2rad($to_geo['latitude'])) * cos(deg2rad($theta));
        $distance = acos($distance);
        $distance = rad2deg($distance);
        $miles = $distance * 60 * 1.1515;
        $miles = floatval(number_format($miles, 2,'.',''));
        return $miles;
    }

}
