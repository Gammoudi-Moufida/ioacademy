<?php

namespace App\Controller;

use App\Entity\Formation;
use App\Entity\Chapter;
use App\Entity\Blog;
use App\Entity\Category;
use App\Entity\TrackFormation;
use App\Entity\TrackChapter;
use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Form\CompleteRegistrationFormType;
use App\Form\FormationFormType;
use App\Form\FormationFormEditType;
use App\Form\FormationFormAdminEditType;
use App\Form\ChapterFormType;
use App\Form\BlogFormType;
use App\Form\CategoryFormType;
use App\Form\UserFormType;
use App\Form\EditUserFormType;
use App\Repository\FormationRepository;
use App\Repository\CategoryRepository;
use App\Repository\ChapterRepository;
use App\Repository\BlogRepository;
use App\Repository\TrackChapterRepository;
use App\Repository\UserRepository;
use App\Repository\TrackFormationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;

class FormationController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function home(FormationRepository $formationRepository): Response
    {
        $formations = $formationRepository->getFirstsActiveFormation();
        $form = $this->createForm(RegistrationFormType::class);
        $form2 = $this->createForm(CompleteRegistrationFormType::class);
        return $this->render('home/index.html.twig', [
            'registrationForm' => $form->createView(),
            'completeRegistration' => $form2->createView(),
            'formations' => $formations, 
        ]);
    }

    #[Route('/formation', name: 'formation', methods: ['GET'])]
    public function getAllActiveFormation(Request $request , FormationRepository $formationRepository , CategoryRepository $categoryRepository,PaginatorInterface $paginator): Response
    {  
        $form = $this->createForm(RegistrationFormType::class);
        $form2 = $this->createForm(CompleteRegistrationFormType::class);
        $level = $request->query->get('level');
        $levels = formation::$levels;
        $categoryId = $request->query->get('category');
        $categorys = $categoryRepository->findActiveCategory(); 
        if($categoryId){
            $selectedCategory = $categoryRepository->findOneById($categoryId);
        }else{
            $selectedCategory = null;
        }
        if (($key = array_search($selectedCategory, $categorys)) !== false) {
            unset($categorys[$key]);
        }
        $res = $formationRepository->formSearch($level,$categoryId);
        $formations = $paginator->paginate(
            $res,
            $request->query->getInt(key: 'page', default: 1),
            limit:10
        );
        return $this->render('formation/index.html.twig', [
            'formations' => $formations, 
            'categorys'=>$categorys,
            'level' => $levels[$level] ?? null,
            'levels' => $levels,
            'selectedCategory' => $selectedCategory,
            'registrationForm' => $form->createView(),
            'completeRegistration' => $form2->createView(),
        ]);
    }

    #[Route('/description/{id}', name: 'description' , methods: ['GET'])]
    public function show(TrackFormationRepository $trackFormationRepository, FormationRepository $formationRepository, TrackChapterRepository $trackChapterRepository, ChapterRepository $chapterRepository, BlogRepository $blogRepository, int $id): Response
    {
        $formation = $formationRepository->find($id);
        if(!$formation){
            throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException('Formation n\'existe pas !');
        }
        $user=$this->getUser();
        $trackformation=null;
        if($user){
            $trackformation=$trackFormationRepository->findOneBy(['formation'=>$formation,'user'=>$user]);
        }
        $percent=null;
        if($trackformation){
            $trackchapters=$trackChapterRepository->findBy(['trackFormations'=>$trackformation,'finished'=>true]);
            $totalchapter=count($formation->getChapters());
            $finishedchapter=count($trackchapters);
            $percent=($finishedchapter/$totalchapter)*100;
        }
        $form = $this->createForm(RegistrationFormType::class); 
        $form2 = $this->createForm(CompleteRegistrationFormType::class);
        $chapters = $chapterRepository->findAll();
        $blogs = $blogRepository->findAll();  
        return $this->render('formation/description.html.twig',[
            'formation' => $formation,
            'chapters' => $chapters,
            'blogs' => $blogs,
            'trackformation' => $trackformation,
            'registrationForm' => $form->createView(),
            'completeRegistration' => $form2->createView(),
            'percent'=>$percent]);
    }

    #[Route('/user/addCourse', name: 'user_add_course')]
    public function addCourse(Request $request, EntityManagerInterface $entityManager, CategoryRepository $categoryRepository): Response
    {  
        if (!$this->isGranted('ROLE_TEACHER') && !$this->isGranted('ROLE_ADMIN') && !$this->isGranted('ROLE_CONTROLLER')) {
            throw new \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException('Access denied');
        }
        $categories = $categoryRepository->findActiveCategory();
        $formation=new Formation();
        $form = $this->createForm(FormationFormType::class,$formation);   
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $formation = $form->getData();
            $uploadedimg = $form['image']->getData();
            $uploadeddoc = $form['document']->getData();
            if ($uploadedimg) {
                $date = new \DateTime();
                $destination = $this->getParameter('kernel.project_dir').'/public/images/formation';
                $newImgname = $date->getTimestamp().'.'.$uploadedimg->guessExtension();
                $uploadedimg->move(
                    $destination,
                    $newImgname
                );
                $formation->setImage($newImgname);
            } 
            if ($uploadeddoc) {
                $date = new \DateTime();
                $destination = $this->getParameter('kernel.project_dir').'/public/document';
                $newFilename = $date->getTimestamp().'_word.'.$uploadeddoc->guessExtension();
                $uploadeddoc->move(
                    $destination,
                    $newFilename
                );
                $formation->setDocument($newFilename);
            } 
            $formation->setUser($this->getUser());
            $formation->setPublicationDate(new \DateTime('now'));
            $formation->setUpdateDate(new \DateTime('now'));
            $entityManager->persist($formation);
            $entityManager->flush();
            unset($formation);
            unset($form);
            $formation = new Formation();
            $form = $this->createForm(FormationFormType::class, $formation);
            $this->addFlash('success-add-course', 'Formation enregistrée avec succées.');   
        }
        return $this->render('formation/new-course.html.twig',[
            'newCourseForm' => $form->createView(),
            'categories' =>$categories,
            'current'=>'addcours',
        ]);
    }

    #[Route('/user/list/formation', name: 'user_list_formations')]
    public function myformations(PaginatorInterface $paginator, Request $request): Response
    {
        if (!$this->isGranted('ROLE_TEACHER')) {
            throw new \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException('Access denied');
        }
        $user=$this->getUser();
        $formations=$user->getFormations();
        $pagination = $paginator->paginate(
            $formations, 
            $request->query->getInt(key: 'page', default: 1),
            limit:6 );
        return $this->render('formation/myformations.html.twig', [
            'formations' => $formations,
            'pagination' => $pagination,
            'current'=>'cours'
        ]);
    }

    #[Route('/user/start/formation/{id}', name: 'startcourse')]
    public function startformations(int $id, FormationRepository $formationRepository): Response
    {
        if (!$this->isGranted('ROLE_TEACHER')  && !$this->isGranted('ROLE_CONTROLLER') && !$this->isGranted('ROLE_ADMIN')) {
            throw new \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException('Access denied');
        }
        $formation=$formationRepository->find($id);
        $chapters=$formation->getChapters();
        return $this->render('formation/startformation.html.twig', [
            'formation' => $formation,
            'chapters' => $chapters,
            'current'=>'cours'
        ]);
    }

    #[Route('/user/edit/formation/{id}', name: 'user_edit_formation')]
    public function editformation(int $id, FormationRepository $formationRepository, Request $request, EntityManagerInterface $entityManager): Response
    {
        if (!$this->isGranted('ROLE_TEACHER')) {
            throw new \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException('Access denied');
        }
        $formation=$formationRepository->find($id);
        $oldimg=$formation->getImage();
        $olddoc=$formation->getDocument();
        if($formation->getUser()==$this->getUser()){
            $form = $this->createForm(FormationFormEditType::class,$formation);   
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $formation = $form->getData();
                $uploadedimg = $form['image']->getData();
                $uploadeddoc = $form['document']->getData();
                $fs = new Filesystem();
                if ($uploadedimg) {
                    $fs->remove($this->getParameter('kernel.project_dir').'/public/images/formation/'.$oldimg);
                    $date = new \DateTime();
                    $destination = $this->getParameter('kernel.project_dir').'/public/images/formation';
                    $newImgname = $date->getTimestamp().'.'.$uploadedimg->guessExtension();
                    $uploadedimg->move(
                        $destination,
                        $newImgname
                    );
                    $formation->setImage($newImgname);
                } 
                if ($uploadeddoc) {
                    $fs->remove($this->getParameter('kernel.project_dir').'/public/document/'.$olddoc);
                    $date = new \DateTime();
                    $destination = $this->getParameter('kernel.project_dir').'/public/document';
                    $newFilename = $date->getTimestamp().'_word.'.$uploadeddoc->guessExtension();
                    $uploadeddoc->move(
                        $destination,
                        $newFilename
                    );
                    $formation->setDocument($newFilename);
                } 
                $formation->setUpdateDate(new \DateTime('now'));
                $entityManager->persist($formation);
                $entityManager->flush();
                $this->addFlash('success-add-course', 'Formation modifiée avec succées.'); 
            }
            return $this->render('formation/editformation.html.twig', [
                'formation' => $formation,
                'formationForm' => $form->createView(),
                'current'=>'cours'
            ]);
        }
        else{
            return $this->render('home/index.html.twig');
        }
       
    }

    #[Route('/admin/edit/formation/{id}', name: 'admin_edit_formation')]
    public function adminEditFormationInfo(int $id, FormationRepository $formationRepository, Request $request, EntityManagerInterface $entityManager): Response
    {
        if (!$this->isGranted('ROLE_ADMIN') && !$this->isGranted('ROLE_CONTROLLER')) {
            throw new \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException('Access denied');
        }
        $formation=$formationRepository->find($id);
        $oldimg=$formation->getImage();
        $olddoc=$formation->getDocument();
        $form = $this->createForm(FormationFormAdminEditType::class,$formation);   
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $formation = $form->getData();
            $active=$form['active']->getData();
            $uploadedimg = $form['image']->getData();
            $uploadeddoc = $form['document']->getData();
            $fs = new Filesystem();
            if ($uploadedimg) {
                $fs->remove($this->getParameter('kernel.project_dir').'/public/images/formation/'.$oldimg);
                $date = new \DateTime();
                $destination = $this->getParameter('kernel.project_dir').'/public/images/formation';
                $newImgname = $date->getTimestamp().'.'.$uploadedimg->guessExtension();
                $uploadedimg->move(
                    $destination,
                    $newImgname
                );
                $formation->setImage($newImgname);
            } 
            if ($uploadeddoc) {
                $fs->remove($this->getParameter('kernel.project_dir').'/public/document/'.$olddoc);
                $date = new \DateTime();
                $destination = $this->getParameter('kernel.project_dir').'/public/document';
                $newFilename = $date->getTimestamp().'_word.'.$uploadeddoc->guessExtension();
                $uploadeddoc->move(
                    $destination,
                    $newFilename
                );
                $formation->setDocument($newFilename);
            } 
            $formation->setActive($active);
            $formation->setUpdateDate(new \DateTime('now'));
            $entityManager->persist($formation);
            $entityManager->flush();
            $this->addFlash('success-edit-course', 'Formation modifiée avec succées.'); 
        }

        return $this->render('formation/admin_edit_formation.html.twig', [
            'formation' => $formation,
            'formationForm' => $form->createView(),
            'current'=>'cours'
        ]);  
    }

    #[Route('admin/formation/invalid', name: 'invalide_formations')]
    public function getInvalidFormations(PaginatorInterface $paginator, Request $request, FormationRepository $formationRepository): Response
    {
        if (!$this->isGranted('ROLE_ADMIN') && !$this->isGranted('ROLE_CONTROLLER')) {
            throw new \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException('Access denied');
        }
        $formations=$formationRepository->findAll();
        $pagination = $paginator->paginate(
            $formations, 
            $request->query->getInt(key: 'page', default: 1),
            limit:6 );
        return $this->render('formation/invalide_formations.html.twig', [
            'pagination' => $pagination,
            'current'=>'cours'
        ]);
    }

    // #[Route('admin/formation/notwrited', name: 'not_writed_formations')]
    // public function getNotWritedFormations(PaginatorInterface $paginator, Request $request, FormationRepository $formationRepository): Response
    // {
    //     if (!$this->isGranted('ROLE_ADMIN')) {
    //         throw new \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException('Access denied');
    //     }
    //     $formations=$formationRepository->getFormationByChampNotActive();
    //     $pagination = $paginator->paginate(
    //         $formations, 
    //         $request->query->getInt(key: 'page', default: 1),
    //         limit:6 );
    //     return $this->render('formation/notwrited_formations.html.twig', [
    //         'pagination' => $pagination,
    //         'current'=>'cours'
    //     ]);
    // }

    #[Route('admin/manage/formation/{id}', name: 'admin__manage_formation')]
    public function adminListChapter(int $id, FormationRepository $formationRepository): Response
    {
        if (!$this->isGranted('ROLE_ADMIN') && !$this->isGranted('ROLE_CONTROLLER')) {
            throw new \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException('Access denied');
        }
        $formation=$formationRepository->find($id);
        $chapters=$formation->getChapters();
        return $this->render('formation/admin_manage_chapter.html.twig',[
            'formation' => $formation,
            'chapters' => $chapters,
            'current'=>'cours'
        ]);
    }

    #[Route('admin/add/chapter/{id}', name: 'admin_add_chapter')]
    public function adminAddchapter(int $id, FormationRepository $formationRepository, Request $request, EntityManagerInterface $entityManager): Response
    {
        if (!$this->isGranted('ROLE_ADMIN') && !$this->isGranted('ROLE_CONTROLLER')) {
            throw new \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException('Access denied');
        }
        $formation=$formationRepository->find($id);
        $chapter=new Chapter();
        $form = $this->createForm(ChapterFormType::class,$chapter);   
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $chapter = $form->getData();
            $chapter->setPublicationDate(new \DateTime('now'));
            $chapter->setFormation($formation);
            $entityManager->persist($chapter);
            $entityManager->flush();
            $this->addFlash('success-add-chapter', 'Chapitre enregistrée avec succées.'); 
        }
        return $this->render('formation/admin_add_chapter.html.twig',[
            'chapterForm' => $form->createView(),
            'formation' => $formation,
            'current'=>'cours'
        ]);
    }

    #[Route('admin/validate/chapter/{id}', name: 'admin_validate_chapter')]
    public function adminValidateChapter(int $id, ChapterRepository $chapterRepository, EntityManagerInterface $entityManager): Response
    {
        if (!$this->isGranted('ROLE_ADMIN') && !$this->isGranted('ROLE_CONTROLLER')) {
            throw new \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException('Access denied');
        }
        $chapter=$chapterRepository->find($id);
        $chapter->setActive(true);
        $entityManager->persist($chapter);
        $entityManager->flush();
        $formation=$chapter->getFormation();
        return $this->redirectToRoute('admin__manage_formation',['id'=>$formation->getId()]);
    }

    #[Route('admin/disable/chapter/{id}', name: 'admin_disable_chapter')]
    public function adminDisableChapter(int $id, ChapterRepository $chapterRepository, EntityManagerInterface $entityManager): Response
    {
        if (!$this->isGranted('ROLE_ADMIN') && !$this->isGranted('ROLE_CONTROLLER')) {
            throw new \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException('Access denied');
        }
        $chapter=$chapterRepository->find($id);
        $chapter->setActive(false);
        $entityManager->persist($chapter);
        $entityManager->flush();
        $formation=$chapter->getFormation();
        return $this->redirectToRoute('admin__manage_formation',['id'=>$formation->getId()]);
    }

    #[Route('admin/list/blog/{id}', name: 'admin_list_blog')]
    public function adminListBlog(int $id, ChapterRepository $chapterRepository): Response
    {
        if (!$this->isGranted('ROLE_ADMIN') && !$this->isGranted('ROLE_CONTROLLER')) {
            throw new \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException('Access denied');
        }
        $chapter=$chapterRepository->find($id);
        $blogs=$chapter->getBlogs();
        return $this->render('formation/admin_manage_blog.html.twig',[
            'chapter' => $chapter,
            'blogs' => $blogs,
            'current'=>'cours'
        ]);
    }

    #[Route('admin/add/blog/{id}', name: 'admin_add_blog')]
    public function adminAddBlog(int $id, ChapterRepository $chapterRepository, Request $request, EntityManagerInterface $entityManager): Response
    {
        if (!$this->isGranted('ROLE_ADMIN') && !$this->isGranted('ROLE_CONTROLLER')) {
            throw new \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException('Access denied');
        }
        $chapter=$chapterRepository->find($id);
        $blog= new Blog();
        $form = $this->createForm(BlogFormType::class,$blog);   
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $blog = $form->getData();
            $blog->setPublicationDate(new \DateTime('now'));
            $blog->setChapter($chapter);
            $entityManager->persist($blog);
            $entityManager->flush();
            $this->addFlash('success-add-blog', 'Blog enregistrée avec succées.'); 
        }
        return $this->render('formation/admin_add_blog.html.twig',[
            'blogForm' => $form->createView(),
            'chapter' => $chapter,
            'current'=>'cours'
        ]);
    }

    #[Route('admin/validate/blog/{id}', name: 'admin_validate_blog')]
    public function adminValidateBlog(int $id, BlogRepository $blogRepository, EntityManagerInterface $entityManager): Response
    {
        if (!$this->isGranted('ROLE_ADMIN') && !$this->isGranted('ROLE_CONTROLLER')) {
            throw new \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException('Access denied');
        }
        $blog=$blogRepository->find($id);
        $blog->setActive(true);
        $entityManager->persist($blog);
        $entityManager->flush();
        $chapter=$blog->getChapter();
        $id=$chapter->getId();
        return $this->redirectToRoute('admin_list_blog',['id'=>$id]);
    }

    #[Route('admin/disable/blog/{id}', name: 'admin_disable_blog')]
    public function adminDisableBlog(int $id, BlogRepository $blogRepository, EntityManagerInterface $entityManager): Response
    {
        if (!$this->isGranted('ROLE_ADMIN') && !$this->isGranted('ROLE_CONTROLLER')) {
            throw new \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException('Access denied');
        }
        $blog=$blogRepository->find($id);
        $blog->setActive(false);
        $entityManager->persist($blog);
        $entityManager->flush();
        $chapter=$blog->getChapter();
        $id=$chapter->getId();
        return $this->redirectToRoute('admin_list_blog',['id'=>$id]);
    }

    #[Route('admin/edit/blog/{id}', name: 'admin_edit_blog')]
    public function adminEditBlog(int $id, BlogRepository $blogRepository, Request $request, EntityManagerInterface $entityManager): Response
    {
        if (!$this->isGranted('ROLE_ADMIN') && !$this->isGranted('ROLE_CONTROLLER')) {
            throw new \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException('Access denied');
        }
        $blog=$blogRepository->find($id);
        $form = $this->createForm(BlogFormType::class,$blog);   
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $blog = $form->getData();
            $blog->setPublicationDate(new \DateTime('now'));
            $entityManager->persist($blog);
            $entityManager->flush();
            $chapter=$blog->getChapter();
            $id=$chapter->getId();
            return $this->redirectToRoute('admin_list_blog',['id'=>$id]);
        }
        return $this->render('formation/admin_edit_blog.html.twig',[
            'blogForm' => $form->createView(),
            'blog'=>$blog,
            'current'=>'cours'
        ]);
    }

    #[Route('admin/edit/chapter/{id}', name: 'admin_edit_chapter')]
    public function adminEditChapter(int $id, ChapterRepository $chapterRepository, Request $request, EntityManagerInterface $entityManager): Response
    {
        if (!$this->isGranted('ROLE_ADMIN') && !$this->isGranted('ROLE_CONTROLLER')) {
            throw new \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException('Access denied');
        }
        $chapter=$chapterRepository->find($id);
        $form = $this->createForm(ChapterFormType::class,$chapter);   
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $chapter = $form->getData();
            $chapter->setPublicationDate(new \DateTime('now'));
            $entityManager->persist($chapter);
            $entityManager->flush();
            $formation=$chapter->getFormation();
            $id=$formation->getId();
            return $this->redirectToRoute('admin__manage_formation',['id'=>$id]);
        }
        return $this->render('formation/admin_edit_chapter.html.twig',[
            'chapterForm' => $form->createView(),
            'chapter'=>$chapter,
            'current'=>'cours'
        ]);
    }

    #[Route('admin/manage/users', name: 'manage_users')]
    public function adminManageUsers(UserRepository $userRepository): Response
    {
        if (!$this->isGranted('ROLE_ADMIN')) {
            throw new \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException('Access denied');
        }
        $users=$userRepository->findAll();
        return $this->render('user/admin_list_users.html.twig',[
            'users'=>$users,
            'current'=>'users'
        ]);
    }

    #[Route('admin/add/user', name: 'admin_add_user')]
    public function adminAddUser(Request $request,EntityManagerInterface $entityManager,UserPasswordHasherInterface $userPasswordHasher): Response
    {
        if (!$this->isGranted('ROLE_ADMIN')) {
            throw new \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException('Access denied');
        }
        $user=new User();
        $form = $this->createForm(UserFormType::class,$user);   
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user = $form->getData();
            $uploadedimg = $form['picture']->getData();
            if ($uploadedimg) {
                $date = new \DateTime();
                $destination = $this->getParameter('kernel.project_dir').'/public/images/userprofile';
                $newImgname = $date->getTimestamp().'_user.'.$uploadedimg->guessExtension();
                $uploadedimg->move(
                    $destination,
                    $newImgname
                );
                $user->setPicture($newImgname);
            } 
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                        $user,
                        $form->get('password')->getData()
                    )
                );
            $role[]=$form['roles']->getData();
            $user->setRoles($role);
            $user->setIsVerified(true);
            $user->setStatus('Active');
            $entityManager->persist($user);
            $entityManager->flush();
            unset($user);
            unset($form);
            $user = new User();
            $form = $this->createForm(UserFormType::class, $user);
            $this->addFlash('success-add-user', 'Utilisateur enregistrée avec succées.');
        }
        return $this->render('user/admin_add_user.html.twig',[
            'userForm' => $form->createView(),
            'current'=>'users'
        ]);
    }

    #[Route('admin/edit/user/{id}', name: 'admin_edit_user')]
    public function adminEditUser(int $id, Request $request, UserRepository $userRepository,EntityManagerInterface $entityManager,UserPasswordHasherInterface $userPasswordHasher): Response
    {
        if (!$this->isGranted('ROLE_ADMIN')) {
            throw new \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException('Access denied');
        }
        $user=$userRepository->find($id);
        $form = $this->createForm(EditUserFormType::class,$user);   
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user = $form->getData();
            $uploadedimg = $form['picture']->getData();
            if ($uploadedimg) {
                $date = new \DateTime();
                $fs = new Filesystem();
                $fs->remove($this->getParameter('kernel.project_dir').'/public/images/userprofile/'.$user->getPicture());
                $destination = $this->getParameter('kernel.project_dir').'/public/images/userprofile';
                $newImgname = $date->getTimestamp().'_user.'.$uploadedimg->guessExtension();
                $uploadedimg->move(
                    $destination,
                    $newImgname
                );
                $user->setPicture($newImgname);
            } 
            $pwd=$form['password2']->getData();
            if($pwd){
                $user->setPassword(
                    $userPasswordHasher->hashPassword(
                            $user,
                            $form->get('password2')->getData()
                        )
                    );
            }
           
            $role[]=$form['roles']->getData();
            $user->setRoles($role);
            $user->setIsVerified(true);
            $user->setStatus('Active');
            $entityManager->persist($user);
            $entityManager->flush();
            $this->addFlash('success-edit-user', 'Utilisateur Modifier avec succées.');   
        }
        return $this->render('user/admin_edit_user.html.twig',[
            'userForm' => $form->createView(),
            'user'=>$user,
            'current'=>'users'
        ]);
    }
    
    #[Route('admin/disable/user/{id}', name: 'admin_disable_user')]
    public function adminDisableUser(int $id, UserRepository $userRepository, EntityManagerInterface $entityManager): Response
    {
        if (!$this->isGranted('ROLE_ADMIN')) {
            throw new \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException('Access denied');
        }
        $user=$userRepository->find($id);
        $user->setStatus('Inactive');
        $entityManager->persist($user);
        $entityManager->flush();
        return $this->redirectToRoute('manage_users');
    }

    #[Route('admin/enable/user/{id}', name: 'admin_enable_user')]
    public function adminEnableUser(int $id, UserRepository $userRepository, EntityManagerInterface $entityManager): Response
    {
        if (!$this->isGranted('ROLE_ADMIN')) {
            throw new \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException('Access denied');
        }
        $user=$userRepository->find($id);
        $user->setStatus('Active');
        $entityManager->persist($user);
        $entityManager->flush();
        return $this->redirectToRoute('manage_users');
    }

    #[Route('admin/remove/user/{id}', name: 'admin_remove_user')]
    public function adminRemoveUser(int $id, UserRepository $userRepository, EntityManagerInterface $entityManager): Response
    {
        if (!$this->isGranted('ROLE_ADMIN')) {
            throw new \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException('Access denied');
        }
        $user=$userRepository->find($id);
        $entityManager->remove($user);
        $entityManager->flush();
        return $this->redirectToRoute('manage_users');
    }

    #[Route('admin/manage/categorie', name: 'manage_categories')]
    public function adminManagecategorie(CategoryRepository $categoryRepository): Response
    {
        if (!$this->isGranted('ROLE_ADMIN')) {
            throw new \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException('Access denied');
        }
        $categories=$categoryRepository->findAll();
        return $this->render('formation/admin_list_category.html.twig',[
            'categories'=>$categories,
            'current'=>'categorie'
        ]);
    }

    #[Route('admin/add/categorie', name: 'add_categorie')]
    public function adminAddcategorie(CategoryRepository $categoryRepository, Request $request, EntityManagerInterface $entityManager): Response
    {
        if (!$this->isGranted('ROLE_ADMIN')) {
            throw new \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException('Access denied');
        }
        $categories=$categoryRepository->findActiveCategory();
        $category=new Category();
        $form = $this->createForm(CategoryFormType::class,$category);   
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $category = $form->getData();
            $category->setActive(false);
            $entityManager->persist($category);
            $entityManager->flush();
            $this->addFlash('success-add-category', 'Catégorie enregistrée avec succées.'); 
            unset($category);
            unset($form);
            $category=new Category();
            $form = $this->createForm(CategoryFormType::class,$category); 
        }
        return $this->render('formation/admin_add_category.html.twig',[
            'categoryForm' => $form->createView(),
            'categories'=>$categories,
            'current'=>'categorie'
        ]);
    }

    #[Route('admin/edit/categorie/{id}', name: 'admin_edit_category')]
    public function adminEditcategorie(int $id,CategoryRepository $categoryRepository, Request $request, EntityManagerInterface $entityManager): Response
    {
        if (!$this->isGranted('ROLE_ADMIN')) {
            throw new \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException('Access denied');
        }
        $categories=$categoryRepository->findActiveCategory();
        $category=$categoryRepository->find($id);
        $form = $this->createForm(CategoryFormType::class,$category);   
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $category = $form->getData();
            $entityManager->persist($category);
            $entityManager->flush();
            return $this->redirectToRoute('manage_categories');
        }
        return $this->render('formation/admin_edit_category.html.twig',[
            'categoryForm' => $form->createView(),
            'category' => $category,
            'categories'=>$categories,
            'current'=>'categorie'
        ]);
    }

    #[Route('admin/validate/categorie/{id}', name: 'admin_validate_category')]
    public function adminValidateCategorie(int $id, CategoryRepository $categoryRepository, EntityManagerInterface $entityManager): Response
    {
        if (!$this->isGranted('ROLE_ADMIN')) {
            throw new \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException('Access denied');
        }
        $category=$categoryRepository->find($id);
        $category->setActive(true);
        $entityManager->persist($category);
        $entityManager->flush();
        return $this->redirectToRoute('manage_categories');
    }

    #[Route('admin/invalidate/categorie/{id}', name: 'admin_invalidate_category')]
    public function adminInvalidateCategorie(int $id, CategoryRepository $categoryRepository, EntityManagerInterface $entityManager): Response
    {
        if (!$this->isGranted('ROLE_ADMIN')) {
            throw new \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException('Access denied');
        }
        $category=$categoryRepository->find($id);
        $category->setActive(false);
        $entityManager->persist($category);
        $entityManager->flush();
        return $this->redirectToRoute('manage_categories');
    }

    #[Route('admin/delete/categorie/{id}', name: 'admin_delete_category')]
    public function adminDeleteCategorie(int $id, CategoryRepository $categoryRepository, EntityManagerInterface $entityManager): Response
    {
        if (!$this->isGranted('ROLE_ADMIN')) {
            throw new \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException('Access denied');
        }
        $category=$categoryRepository->find($id);
        $entityManager->remove($category);
        $entityManager->flush();
        return $this->redirectToRoute('manage_categories');
    }

    #[Route('user/validate/formation/{id}', name: 'prof_validate_formation')]
    public function userValidateFormation(int $id, FormationRepository $formationRepository, EntityManagerInterface $entityManager): Response
    {
            if (!$this->isGranted('ROLE_TEACHER')) {
                throw new \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException('Access denied');
            }
            $formation=$formationRepository->find($id);
            $formation->setActive(2);
            $entityManager->persist($formation);
            $entityManager->flush();
            return $this->redirectToRoute('user_list_formations');       
    }

    #[Route('search/formation/{query}', name: 'autocomplete-formation')]
    public function autocompleteFormation(string $query, FormationRepository $formationRepository)
    {
            $formations=$formationRepository->findMatching($query);
             $data = [];
             foreach($formations as $formation){
                $data[] = array('label'=>$formation->getTitle(),'value'=>$formation->getId());
             }
             $data =  json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
             return new JsonResponse($data, 200, [], true);
    }

    #[Route('/formation/inscription/{idformation}', name: 'inscription_formation')]
    public function subscribe(TrackFormationRepository $trackFormationRepository,EntityManagerInterface $entityManager, FormationRepository $formationRepository, $idformation): Response
    {
        $formation= $formationRepository->find($idformation);
        if($formation != null){
           $trackformation=$trackFormationRepository->findOneBy(['formation'=>$formation,'user'=>$this->getUser()]);
           if($trackformation == null){
                $trackformation = new TrackFormation();
                $trackformation->setStartTime(new \DateTime());
                $trackformation->setUser($this->getUser());
                $trackformation->setFormation($formation);
                $trackformation->setStatus('started');
                $entityManager->persist($trackformation);
                $entityManager->flush();
                $formation->addTrackformation($trackformation);
                $entityManager->persist($formation);
                $entityManager->flush();

           }
           return $this->redirectToRoute('start_read_formation',['idformation' => $formation->getId()]);
        }
        throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException('Formation n\'existe pas !');
    }

    #[Route('/formation/{idformation}', name: 'start_read_formation', defaults:['idchapter'=>null,'idblog'=>null], methods: ['GET'])]
    #[Route('/formation/{idformation}/chapter/{idchapter}', name: 'start_read_chapter', defaults:['idblog'=>null], methods: ['GET'])]
    #[Route('/formation/{idformation}/blog/{idblog}', name: 'start_read_blog',defaults:['idchapter'=>null], methods: ['GET'])]
    public function showChapter(FormationRepository $formationRepository,TrackFormationRepository $trackFormationRepository,
                                TrackChapterRepository $trackChapterRepository,ChapterRepository $chapterRepository, BlogRepository $blogRepository, $idformation, 
                                $idchapter=null, $idblog=null): Response
    {
        $user=$this->getUser();
        $formation= $formationRepository->find($idformation);
        if($formation==null){
            throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException('Formation n\'existe pas !');
        }
        if(!$idchapter && !$idblog){
            $trackformation=$trackFormationRepository->findOneBy(['formation'=>$formation,'user'=>$user]);
            $trackchapters=$trackChapterRepository->findBy(['trackFormations'=>$trackformation,'finished'=>true]);
            $totalchapter=count($formation->getChapters());
            if($trackchapters && count($trackchapters)<$totalchapter)
            {
            $orderchap = $trackchapters[count($trackchapters)-1]->getChapter()->getOrderChapter();
            $currentchapter = $chapterRepository->findOneBy(['orderChapter' => $orderchap+1, 'formation'=>$formation]);
            $blogs=$currentchapter->getBlogs();
            $currentblog=$blogs[0];
            }
            else{
            $chapters=$formation->getChapters();
            $currentchapter=$chapters[0];
            $blogs=$currentchapter->getBlogs();
            $currentblog=$blogs[0];  
            }
        }
        else if($idchapter && !$idblog){
            $currentchapter = $chapterRepository->find($idchapter);
            $blogs=$currentchapter->getBlogs();
            $currentblog=$blogs[0];
        }
        else if(!$idchapter && $idblog){
            $currentblog = $blogRepository->find($idblog);
            $currentchapter = $currentblog->getChapter();
        }
        else {
            $currentblog = $blogRepository->find($idblog);
            $currentchapter = $chapterRepository->find($idchapter);
        } 
        $nextblog=null;
        if($currentblog->getOrderBlog()<count($currentchapter->getBlogs())){
            $order=$currentblog->getOrderBlog()+1;
            $nextblog=$blogRepository->findOneBy(['orderBlog' => $order, 'chapter'=>$currentchapter]);
        }
        $prevblog=null;
        if($currentblog->getOrderBlog()>1 && count($currentchapter->getBlogs())>1){
            $order=$currentblog->getOrderBlog()-1;
            $prevblog=$blogRepository->findOneBy(['orderBlog' => $order, 'chapter'=>$currentchapter]);
        }
        $trackformation=null;
        
        if($user){
            $trackformation=$trackFormationRepository->findOneBy(['formation'=>$formation,'user'=>$user]);
        }
        $percent=null;
        if($trackformation){
            $trackchapters=$trackChapterRepository->findBy(['trackFormations'=>$trackformation,'finished'=>true]);
            $totalchapter=count($formation->getChapters());
            $finishedchapter=count($trackchapters);
            $percent=($finishedchapter/$totalchapter)*100;
        }
        return $this->render('formation/details.html.twig',[
            'formation' => $formation,
            'currentchapter'=>$currentchapter,
            'currentblog'=>$currentblog,
            'nextblog' => $nextblog,
            'prevblog'=>$prevblog,
            'percent'=>$percent]);
    }

    #[Route('/formation/finishchapter/{idformation}/{idchapter}', name: 'finish_chapter')]
    public function finishedChapter(TrackChapterRepository $trackChapterRepository,TrackFormationRepository $trackFormationRepository,ChapterRepository $chapterRepository,EntityManagerInterface $entityManager, FormationRepository $formationRepository, $idformation ,$idchapter): Response
    {
        $formation= $formationRepository->find($idformation);
        $chapter= $chapterRepository->find($idchapter);
        if( $formation && $chapter ){
            $trackformation=$trackFormationRepository->findOneBy(['formation'=>$formation,'user'=>$this->getUser()]);
            $trackmychapter=$trackChapterRepository->findOneBy(['trackFormations'=>$trackformation,'chapter'=>$chapter]);
            if(!$trackmychapter){
                $trackchapter = new TrackChapter();
                $trackchapter->setTrackFormations($trackformation);
                $trackchapter->setChapter($chapter);
                $trackchapter->setFinished(true);
                $entityManager->persist($trackchapter);
                $entityManager->flush();  
            }
            $nextchapter=null;
            if($chapter->getOrderChapter()<count($formation->getChapters())){
                $order=$chapter->getOrderChapter()+1;
                $nextchapter=$chapterRepository->findOneBy(['orderChapter' => $order, 'formation'=>$formation]);
                return $this->redirectToRoute('start_read_chapter',['idformation' => $formation->getId(),'idchapter'=>$nextchapter->getId()]);
            }
           return $this->redirectToRoute('start_read_chapter',['idformation' => $formation->getId()]);
        }
        throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException('Formation n\'existe pas !');
    }

    #[Route('/finishformation/{idformation}', name: 'finish_formation')]
    public function finishFormation(TrackFormationRepository $trackFormationRepository,EntityManagerInterface $entityManager, FormationRepository $formationRepository, $idformation): Response
    {
        $formation= $formationRepository->find($idformation);
        if($formation != null){
            $trackformation=$trackFormationRepository->findOneBy(['formation'=>$formation,'user'=>$this->getUser()]);
            if($trackformation->getStatus()=='started'){
                $date = new \DateTime();
                $trackformation->setEndTime($date);
                $trackformation->setStatus('finished');
                $entityManager->persist($trackformation);
                $entityManager->flush();
                $chapters=$formation->getChapters();
                $lastchapter=$chapters[count($chapters)-1];
                $trackchapter = new TrackChapter();
                $trackchapter->setTrackFormations($trackformation);
                $trackchapter->setChapter($lastchapter);
                $trackchapter->setFinished(true);
                $entityManager->persist($trackchapter);
                $entityManager->flush();
            }
           return $this->redirectToRoute('description',['id' => $formation->getId()]);
        }
        throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException('Formation n\'existe pas !');
    }

    #[Route('/user/current/courses', name: 'user_current_courses')]
    public function mycourses(TrackFormationRepository $trackFormationRepository): Response
    {
        
        $this->denyAccessUnlessGranted('ROLE_STUDENT');
        $user=$this->getUser();
        // $trackformations=$trackFormationRepository->findBy(['user'=>$user]);
        $trackformations=$trackFormationRepository->findDetail(['id'=>$user->getId()]);
        return $this->render('user/currentcourses.html.twig', [
            'controller_name' => 'CurrentCoursesController',
            'current' => 'cours',
            'formations' =>$trackformations
        ]);
    }

    #[Route('/menu/not-accesible', name: 'not-accessible')]
    public function notAccessible(): Response
    {
        return $this->render('formation/not-accessible.html.twig',[
            'current' => 'vide'
        ]);
    }
}
