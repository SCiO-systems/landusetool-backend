<?php

namespace App\Utilities\SCIO;

class WocatTransformer
{
    protected $data;
    protected $baseURI;

    public function __construct($data, $baseURI = 'https://qcat.wocat.net')
    {
        $this->data = $data;
        $this->baseURI = $baseURI;
    }

    protected function extractImages($item)
    {
        $extractedImages = [];
        $images = data_get(
            $item,
            'section_specifications.tech__2.tech__2__3.qg_photos.image.value'
        );

        $captions = data_get(
            $item,
            'section_specifications.tech__2.tech__2__3.qg_photos.image_caption.value'
        );

        $index = 0;
        foreach ($images as $image) {
            $caption = $captions[$index];
            $extractedImages[] = [
                'url' => $this->baseURI . data_get($image, 'value'),
                'caption' => data_get($caption, 'value'),
            ];
            $index++;
        }

        return $extractedImages;
    }

    public function getTransformedOutput()
    {
        $transformed = null;

        foreach ($this->data as $item) {
            $id = data_get($item, 'techId');

            $images = $this->extractImages($item);

            $transformed[] = [
                'id' => $id,
                'url' => $this->baseURI . '/en/wocat/technologies/view/' . $id,
                'map_url' => $this->baseURI . '/en/wocat/technologies/view/' . $id . '/map',
                'name' => data_get(
                    $item,
                    'section_general_information.tech__1.tech__1__1.qg_name.name.value.0.value'
                ),
                'local_name' => data_get(
                    $item,
                    'section_general_information.tech__1.tech__1__1.qg_name.name_local.value.0.value'
                ),
                'definition' => data_get(
                    $item,
                    'section_specifications.tech__2.tech__2__1.tech_qg_1.tech_definition.value.0.value'
                ),
                'description' => data_get(
                    $item,
                    'section_specifications.tech__2.tech__2__2.tech_qg_2.tech_description.value.0.value'
                ),
                'location' => data_get(
                    $item,
                    'section_specifications.tech__2.tech__2__5.qg_location.country.value.0.value'
                ),
                'province' => data_get(
                    $item,
                    'section_specifications.tech__2.tech__2__5.qg_location.state_province.value.0.value'
                ),
                'location_comments' => data_get(
                    $item,
                    'section_specifications.tech__2.tech__2__5.tech_qg_225.location_comments.value.0.value'
                ),
                'implementation_period' => data_get(
                    $item,
                    'section_specifications.tech__2.tech__2__6.tech_qg_160.tech_implementation_decades.value.0.values.0.0'
                ),
                'images' => $images,
                'econ_wocat' => data_get(
                    $item,
                    'econ_wocat',
                ),
            ];
        }

        return $transformed;
    }
}
