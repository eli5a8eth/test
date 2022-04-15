<?php

namespace App;

use App\Entity\Product;
use App\Entity\Seller;
use Symfony\Component\HttpFoundation\Response;
use GuzzleHttp\Client;
use Symfony\Component\DomCrawler\Crawler;
use App\Controller\Admin\ParserController;
//use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\Persistence\ManagerRegistry;

class ParserService
{
    private ManagerRegistry $doctrine;
    private $em;
    private $entityManager;

    public function __construct(ManagerRegistry $doctrine) {

        return $this->doctrine = $doctrine;
    }

    /**
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function collect($url): int
    {
        $client = new Client(['base_uri' => $url]);
        $html = (string) $client->get($url)->getBody();

        $crawler = new Crawler($html);

        $node = $crawler->filterXPath('//*[@id="state-searchResultsV2-252189-default-1"]')->outerHtml();
        $encode = stristr($node, '{"items');
        $newEncode = stristr($encode, '\'></div>', true);
        $items = (array) json_decode($newEncode, true)['items'];

        $productData = [];

        $size = count($items);

        for ($i = 0; $i < $size; $i++) {

            if ($this->arrayFind('В корзину', $items[$i]) && $this->arrayFind('textAtomWithIcon', $items[$i])) {

                if (!$this->arrayFind('rating', $items[$i])) {
                    $productData[$i]['reviews_count'] = 0;
                }
                foreach ($items[$i]['mainState'] as $mainState) {
                    if ($mainState['id'] === 'name') {
                        $name = $mainState['atom']['textAtom']['text'];
                        $productData[$i]['name'] = $name;
                    }
                    if ($mainState['atom']['type'] === 'rating') {
                        $reviews = (int)$mainState['atom']['rating']['count'];
                        $productData[$i]['reviews_count'] = $reviews;
                    }

                }
                $price = $items[$i]['mainState'][0]['atom']['price']['price'];
                $productData[$i]['price'] = (int) str_replace([' ₽', ' '], '', $price);
                $productData[$i]['sku'] = (int) $items[$i]['topRightButtons'][0]['favoriteProductMolecule']['sku'];
                $seller = $items[$i]['multiButton']['ozonSubtitle']['textAtomWithIcon']['text'];
                $seller = strip_tags($seller);
                $seller = stristr($seller, 'продавец');
                $seller = str_replace('продавец ', '', $seller);
                $productData[$i]['seller'] = $seller;

            }

        }

        $this->saveProduct($productData);

        return $size;
    }

    private function saveProduct($productData)
    {
        $doctrine = $this->doctrine;

        $entityManager = $doctrine->getManager();

        foreach ($productData as $item) {

            $seller = $entityManager->getRepository(Seller::class)->findOneBy(['name' => $item['seller']]);
            if (empty($seller)) {
                $seller = new Seller;
                $seller->setName($item['seller']);
            }
            $entityManager->persist($seller);
            $entityManager->flush();

            $product = $entityManager->getRepository(Product::class)->findOneBy(['sku' => $item['sku']]);
            if (empty($product)) {
                $product = new Product;
                $product->setName($item['name']);
                $product->setPrice($item['price']);
                $product->setSku($item['sku']);
                $product->setReviewsCount($item['reviews_count']);
                $product->setCreatedAtValue();
                $product->setUpdatedAtValue();
                $product->setSellerId($seller);
            }
            $entityManager->persist($product);
            $entityManager->flush();
        }

    }

    public function arrayFind(mixed $needle, array $haystack): bool
    {
        foreach ($haystack as $value) {
            if (is_array($value)) {
                if (in_array($needle, $value)) {
                    return true;
                } else {
                    if ($this->arrayFind($needle, $value)) {
                        return true;
                    }
                }
            } else {
                if ($value == $needle) {
                    return true;
                }
            }
        }
        return false;
    }

    public static function isAbsolute(string $url): bool
    {
        $pattern = "/^(?:ftp|https?|feed)?:?\/\/(?:(?:(?:[\w\.\-\+!$&'\(\)*\+,;=]|%[0-9a-f]{2})+:)*
        (?:[\w\.\-\+%!$&'\(\)*\+,;=]|%[0-9a-f]{2})+@)?(?:
        (?:[a-z0-9\-\.]|%[0-9a-f]{2})+|(?:\[(?:[0-9a-f]{0,4}:)*(?:[0-9a-f]{0,4})\]))(?::[0-9]+)?(?:[\/|\?]
        (?:[\w#!:\.\?\+\|=&@$'~*,;\/\(\)\[\]\-]|%[0-9a-f]{2})*)?$/xi";

        if (preg_match($pattern, $url)) {
            return true;
        }
        return false;
    }


}
