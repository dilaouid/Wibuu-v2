<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Post;
use App\Entity\Filters;
use App\Entity\IdentificationPost;

use App\Form\PostType;
use App\Repository\UserRepository;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\Validation;

use Doctrine\ORM\EntityManagerInterface;

class SubmitController extends AbstractController
{

    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var Environment
     */
    private $twig;

    public function __construct(EntityManagerInterface $em) 
    {
        $this->em       = $em;
    }

    /**
     * @Route("/submit", name="submit")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function submit(Request $request): Response
    {
        $user = $this->getUser();
        $post = new Post();
        $filters = new Filters();
        $repositoryFilters = $this->getDoctrine()->getRepository(Filters::class);
        $repositoryUsers = $this->getDoctrine()->getRepository(User::class);
        $filters = $repositoryFilters->findAll();
        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $postUuid = uniqid();
                $img        = $form->get('file')->getData();
                $base64     = $form->get('base64')->getData();
                $posX       = $form->get('posX')->getData();
                $posY       = $form->get('posY')->getData();
                $width      = $form->get('size')->getData();
                try {
                    $img->move(
                        $this->getParameter('img_directory') . 'publications/' . $user->getUuid() . '/original',
                        $postUuid . '.jpeg'
                    );
                } catch (FileException $e) {
                    dump('An error occured -> ' . $e);
                }

                $identifications = $form->get('identificationPosts')->getData();
                if (!empty($identifications)) {
                    $identifications    = explode(' ', $identifications);
                    $identificationObj  = new IdentificationPost();
                    for ($i = 0; $i < count($identifications); $i++) {
                        $findUser = $repositoryUsers->findOneBy(["username" => $identifications[$i]]);
                        if (!$findUser) {
                            $this->addFlash(
                                'warning',
                                "Un utilisateur saisi en identification n'existe pas."
                            );
                            return $this->render('home/pages/submit.html.twig', [
                                'user' => $user,
                                'filters' => $filters,
                                'pageNav' => 'submit',
                                'searchbar' => ['status' => false, 'value' => null],
                                'form'    => $form->createView()
                            ]);
                        }
                        $identificationObj->setPost($post)
                                          ->addUser($findUser);
                    }
                    $this->em->persist($identificationObj);
                }
                $form->get('private')->getData() === true ? $pass = uniqid() : $pass = '';
                $filter_id = $repositoryFilters->findOneBy(['id' => $form->get('filterH')->getData()]);
                if (!$filter_id) {
                    $this->addFlash(
                        'warning',
                        'Tout les champs doivent être correctement remplis.'
                    );
                    return $this->render('home/pages/submit.html.twig', [
                        'user' => $user,
                        'filters' => $filters,
                        'pageNav' => 'submit',
                        'searchbar' => ['status' => false, 'value' => null],
                        'form'    => $form->createView()
                    ]);
                }
                
                $pathImg        = $this->getParameter('img_directory') . 'publications/' . $user->getUuid() . '/original/' . $postUuid . '.jpeg';
                $pathPost       = $this->getParameter('img_directory') . 'publications/' . $user->getUuid() . '/';
                $this->createImageFromSubmission($pathPost, $base64, $postUuid);

                $pathFilter     = $this->getParameter('filters_directory') . '/' . $form->get('filterH')->getData() . '.png';

                $filter         = imagecreatefrompng($pathFilter);
                $filter_resized = imagescale($filter, $width);
                imagepng($filter_resized, $pathPost . 'filter_resized.png', 9);
                imagedestroy($filter_resized);

                list($width, $height) = getimagesize($pathPost . 'filter_resized.png');

                $correctedPNGFilter = imagecreatetruecolor($width, $height);
                imagealphablending($correctedPNGFilter , false);
                imagesavealpha($correctedPNGFilter , true);
                imagecopyresampled($correctedPNGFilter, $filter, 0, 0, 0, 0, $width, $height, 455, 286);
                imagealphablending($correctedPNGFilter , false);
                imagesavealpha($correctedPNGFilter , true);
                imagepng($correctedPNGFilter, $pathPost . $postUuid .'_filter.png', 9);
                imagedestroy($correctedPNGFilter);
                unlink($pathPost . 'filter_resized.png');
                
                $resizedFilter      = imagecreatefrompng($pathPost . $postUuid .'_filter.png');

                list($widthOr, $heightOr)   = getimagesize($pathPost . $postUuid .'.jpeg');
                $noFilterImage              = imagecreatefromjpeg($pathPost . $postUuid.'.jpeg');
                $OriginalPicture_resized    = imagescale($noFilterImage, 570);

                $this->imagecopymerge_alpha($OriginalPicture_resized, $resizedFilter, $posX, $posY, 0, 0, $width, $height, 100);
                imagejpeg($OriginalPicture_resized, $pathPost . $postUuid .'.jpeg', 100);
                imagedestroy($OriginalPicture_resized);
                unlink($pathPost . $postUuid .'_filter.png');
                $time = date('Y-m-d h:i:s', time());

                $post->setAuthor($user)
                    ->setDescription($form->get('description')->getData())
                    ->setPosition($form->get('position')->getData())
                    ->setPrivate($form->get('private')->getData())
                    ->setPass($pass)
                    ->setViews(0)
                    ->setUuid($postUuid)
                    ->setFilters($filter_id)
                    ->setTags($filter_id->getTags())
                    ->setMoment(date());
                $this->em->persist($post);
                $user->setPublications($user->getPublications() + 1);
                $this->em->persist($user);
                $this->em->flush();
            } else {
                $this->addFlash(
                    'warning',
                    'Tout les champs doivent être correctement remplis.'
                );
            }
        }
        return $this->render('home/pages/submit.html.twig', [
            'user' => $user,
            'filters' => $filters,
            'pageNav' => 'submit',
            'searchbar' => ['status' => false, 'value' => null],
            'form'    => $form->createView()
        ]);
    }

    private function createImageFromSubmission($pathImg, $img, $postUuid)
    {

        $image_parts    = explode(";base64,", $img);
        $image_type_aux = explode("image/", $image_parts[0]);
        $image_type     = $image_type_aux[1];
        $image_base64   = base64_decode($image_parts[1]);

        $ext            = explode('/', $image_parts[0])[1];

        $fileName       = $postUuid . '.' . $ext;
        $file           = $pathImg . $fileName;
        file_put_contents($file, $image_base64);

        switch($ext)
        {
            case 'png':
                $webcamPhotoWebP = imagecreatefrompng($file);
                imagejpeg($webcamPhotoWebP, $pathImg . $postUuid .'.jpeg', 100);
                break;
                break;
            case 'jpg':
                $webcamPhotoWebP = imagecreatefromjpeg($file);
                imagejpeg($webcamPhotoWebP, $pathImg . $postUuid .'.jpeg', 100);
                break;
            case 'jpeg':
                $webcamPhotoWebP = imagecreatefromjpeg($file);
                imagejpeg($webcamPhotoWebP, $pathImg . $postUuid .'.jpeg', 100);
                break;
        }
        $destroy = imagedestroy($webcamPhotoWebP);
    }

    private function imagecopymerge_alpha($dst_im, $src_im, $dst_x, $dst_y, $src_x, $src_y, $src_w, $src_h, $pct)
    {
        $cut = imagecreatetruecolor($src_w, $src_h);
        imagecopy($cut, $dst_im, 0, 0, $dst_x, $dst_y, $src_w, $src_h);
        imagecopy($cut, $src_im, 0, 0, $src_x, $src_y, $src_w, $src_h);
        imagecopymerge($dst_im, $cut, $dst_x, $dst_y, 0, 0, $src_w, $src_h, $pct);
    }

}