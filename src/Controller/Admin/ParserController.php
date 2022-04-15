<?php

namespace App\Controller\Admin;

use App\ParserService;
use DOMDocument;
use DOMXPath;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use GuzzleHttp\Exception\GuzzleException;
use PHPStan\Type\ErrorType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Seller;
use App\Entity\Product;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use GuzzleHttp\Client;
use Twig\Environment;
use App\Repository\SellerRepository;

class ParserController extends AbstractDashboardController
{

    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {
        return parent::index();
    }


    /**
     * @throws GuzzleException
     */
    #[Route('/parser', name: 'form')]
    public function getUrl(Request $request, ManagerRegistry $doctrine): Response
    {
        $form = $this->createFormBuilder()
            ->add('url', UrlType::class)
            ->add('submit', SubmitType::class)
            //->add('error', ErrorType::class);
            ->getForm();

        $form->handleRequest($request);

        $parser = new ParserService($doctrine);

        if ($form->isSubmitted() && $form->isValid()) {
            (string) $url = $request->request->all('form')['url'];

            if ($parser->isAbsolute($url)) {
                $size = $parser->collect($url);
            }

        }

        return $this->render('admin/form.html.twig', [
            'form' => $form->createView(),
            'size' => $size,
        ]);
    }


    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Parser');
    }

    public function configureMenuItems(): iterable
    {
        //yield MenuItem::linkToDashboard('Parser', 'fa fa-home');
        //yield MenuItem::linkToCrud('The Label', 'fas fa-list', EntityClass::class);
        yield MenuItem::linkToRoute('Parser', 'fas fa-map-marker-alt', 'parser');
        yield MenuItem::linkToCrud('Products', 'fas fa-map-marker-alt', Product::class);
        //yield MenuItem::linkToCrud('Parser', 'fas fa-map-marker-alt', ParserController::class);
    }

}
