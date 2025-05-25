<?php

namespace App\Controller\Admin;

use App\Entity\Car;
use App\Entity\User;
use App\Entity\Image;
use App\Entity\Categorie;
use App\Entity\Commentaire;
use App\Entity\PerformanceCar;
use App\Entity\PerformanceType;
use App\Entity\MotorisationType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminDashboard;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;



#[Route('/admin', name: 'admin')]
#[AdminDashboard(routePath: '/admin', routeName: 'admin')]
class DashboardController extends AbstractDashboardController
{
    public function index(): Response
    {
        // return parent::index();
return $this->render('admin/index.html.twig');
        // Option 1. You can make your dashboard redirect to some common page of your backend
        //
        // 1.1) If you have enabled the "pretty URLs" feature:
        // return $this->redirectToRoute('admin_user_index');
        //
        // 1.2) Same example but using the "ugly URLs" that were used in previous EasyAdmin versions:
        // $adminUrlGenerator = $this->container->get(AdminUrlGenerator::class);
        // return $this->redirect($adminUrlGenerator->setController(OneOfYourCrudController::class)->generateUrl());

        // Option 2. You can make your dashboard redirect to different pages depending on the user
        //
        // if ('jane' === $this->getUser()->getUsername()) {
        //     return $this->redirectToRoute('...');
        // }

        // Option 3. You can render some custom template to display a proper dashboard with widgets, etc.
        // (tip: it's easier if your template extends from @EasyAdmin/page/content.html.twig)
        //
        // return $this->render('some/path/my-dashboard.html.twig');
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('CarMette');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');
        yield MenuItem::section('Gestion des utilisateurs');
    yield MenuItem::linkToCrud('Utilisateurs', 'fas fa-users', User::class);

    yield MenuItem::section('Voitures');
    yield MenuItem::linkToCrud('Voitures', 'fas fa-car', Car::class);
    yield MenuItem::linkToCrud('Catégories', 'fas fa-layer-group', Categorie::class);
    yield MenuItem::linkToCrud('Motorisations', 'fas fa-cogs', MotorisationType::class);
    yield MenuItem::linkToCrud('Performances', 'fas fa-tachometer-alt', PerformanceCar::class);
    yield MenuItem::linkToCrud('Types de performance', 'fas fa-list-ol', PerformanceType::class);
    yield MenuItem::linkToCrud('Images', 'fas fa-image', Image::class);

    yield MenuItem::section('Commentaires');
    yield MenuItem::linkToCrud('Commentaires', 'fas fa-comment', Commentaire::class);
        // yield MenuItem::linkToCrud('The Label', 'fas fa-list', EntityClass::class);
    }
}
