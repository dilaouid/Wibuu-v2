<?php

namespace App\Controller;

use App\Entity\SideImg;
use App\Entity\Filters;
use App\Form\SideImgType;
use App\Form\FiltersType;

use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

use Doctrine\ORM\EntityManagerInterface;

class AdminPanelController extends AbstractController
{
    /**
     * @var Environment
     */
    private $twig;

    /**
     * @var EntityManagerInterface
     */
    private $em;

    private $pageInfos = array();
    private $sidebar = [
        ["name"     => 'Panneau de contrôle',
         "icon"     => 'fas fa-tachometer-alt',
         "route"    => '/admin'
        ],
        ["name"     => 'Design',
         "icon"     => 'fas fa-paint-brush',
         "route"    => '/admin/design'
        ],
        ["name"     => 'Utilisateurs',
         "icon"     => 'far fa-user-circle',
         "route"    => '/admin/users'
        ],
        ["name"     => 'Retour au site',
         "icon"     => 'fas fa-home',
         "route"    => '/'
        ]
    ];

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @Route("/admin/sideimg/new", name="admin.sideimg.new")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function newSideImg(Request $request): Response
    {
        $sideimg = new SideImg();
        $form = $this->createForm(SideImgType::class, $sideimg);
        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            if ($form->isValid()) {

                $imgsideFile = $form->get('file')->getData();
                $filename = uniqid() . '.jpg';
                try {
                    $imgsideFile->move(
                        $this->getParameter('sideimg_directory'),
                        $filename
                    );
                } catch (FileException $e) {
                    dump('An error occured -> ' . $e);
                }
                $sideimg->setFilename($filename);
                $this->em->persist($sideimg);
                $this->em->flush();
                $this->addFlash(
                    'success',
                    'La nouvelle image a bien été rajoutée !'
                );
            } else {
                $this->addFlash(
                    'warning',
                    'Tout les champs doivent être correctement remplis.'
                );
            }
        }
        $this->pageInfos = $this->pageInfos + [
            "section" => 'Design',
            'pagename' => 'Ajouter une image de côté',
            'titleContent' => 'Ajouter une image de côté',
            'previewLink' => '/assets/img/sideImg/template.jpg',
            'btn' => 'Enregistrer cette image',
            'previewClass' => 'sideImgManage'
        ];

        return $this->render("admin/manage_img.html.twig", [
            'data'      => $this->pageInfos,
            'sidebar'   => $this->sidebar,
            'form'      => $form->createView()
        ]);
    }

    /**
     * @Route("/admin/filters/new", name="admin.filter.new")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function newFilter(Request $request): Response
    {
        $filter = new Filters();
        $tagsAsArray = [];
        $form = $this->createForm(FiltersType::class, $filter);
        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $con = $this->getDoctrine()
                            ->getManager()
                            ->getConnection()
                            ->prepare("SELECT AUTO_INCREMENT as last_id FROM INFORMATION_SCHEMA.TABLES WHERE table_name = 'filters'");
                $con->execute();
                $nextID = $con->fetch()['last_id'];
                
                $imgsideFile = $form->get('file')->getData();
                $filename = $nextID . '.png';
                try {
                    $imgsideFile->move(
                        $this->getParameter('filters_directory'),
                        $filename
                    );
                } catch (FileException $e) {
                    dump('An error occured -> ' . $e);
                }
                $this->em->persist($filter);
                $this->em->flush();
                $this->addFlash(
                    'success',
                    'Le nouveau filtre a bien été installé !'
                );
            } else {
                $this->addFlash(
                    'warning',
                    'Tout les champs doivent être correctement remplis.'
                );
            }
        }
        $this->pageInfos = $this->pageInfos + [
            "section" => 'Design',
            'pagename' => 'Ajouter un filtre',
            'titleContent' => 'Ajouter un filtre',
            'previewLink' => '/assets/img/filters/preview.png',
            'btn' => 'Enregistrer ce filtre',
            'previewClass' => 'filterManage'
        ];

        return $this->render("admin/manage_img.html.twig", [
            'data'      => $this->pageInfos,
            'sidebar'   => $this->sidebar,
            'form'      => $form->createView(),
            'flashtype' => ''
        ]);
    }

}