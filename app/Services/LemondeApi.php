<?php


namespace App\Services;


class LemondeApi implements Api
{
    /**
     * @var array
     */
    private $data = [];

    public function __construct()
    {
        $url = 'https://www.lemonde.fr/rss/une.xml';
        $reader = \XMLReader::open($url);
        while ($reader->read() && $reader->name !== 'item') {
        }
        $id = 0;
        while ($reader->name === 'item') {
            $element = new \SimpleXMLElement($reader->readOuterXML());
            $namespaces = $element->getNameSpaces(true);
            $media = $element->children($namespaces['media']);
            $this->data[] = [
                "id" => $id++,
                'title' => (string)$element->title,
                'description' => (string)$element->description,
                'url' => (string)$media->content->attributes()['url']
            ];
            $reader->next('item');
        }
        $reader->close();
    }

    public function getAll(): array
    {
        return $this->data;
    }

    public function getOne($id): array
    {
        $return = array_filter($this->data, fn($data) => $data['id']==$id);
        return empty($return)?$return : array_pop($return);
    }
}
