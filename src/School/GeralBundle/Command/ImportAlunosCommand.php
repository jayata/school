<?php
/**
 * Created by PhpStorm.
 * User: ayata
 * Date: 30/01/18
 * Time: 14:26
 */

namespace School\GeralBundle\Command;

use School\AlunoBundle\Entity\Aluno;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Doctrine\ORM\EntityManagerInterface;
use League\Csv\Reader;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Helper\ProgressBar;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Symfony\Component\DependencyInjection\Container;

class ImportAlunosCommand extends Command
{
    public function __construct(EntityManagerInterface $em, Container $container)
    {
        parent::__construct();
        $this->em = $em;
        $this->container = $container;
    }

    protected function configure()
    {
        $this
            ->setName('school:import:students')
            ->addArgument('file', InputArgument::OPTIONAL, 'The file to input')
            ->setDescription('Imports alunos from csv')
            ->setHelp('This command allows you to import alunos to the system');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $io->title("Atempting to upload the students");

        $file = $input->getArgument('file');
        if (is_null($file)){
            $appPath = $this->container->getParameter('kernel.root_dir');
            $file= $appPath.'/../src/School/GeralBundle/Data/students_file.csv';
        }
        if (file_exists($file) && is_file($file) && pathinfo($file)['extension'] == 'csv') {

            $csv = Reader::createFromPath($file);
            $csv->setDelimiter(';');
            $csv->setHeaderOffset(0);

            $count = $csv->count();
            $records = $csv->getRecords();
            $batchSize = 15;
            $currentSize = 0;
            $pattern = '/^(\d{3}\.\d{3}\.\d{3}\-\d{2})$/';
            $cpfs = array();

            $progress = new ProgressBar($output, $count);
            $progress->start();
            foreach ($records as $row) {
                $cpf = $row['cpf'];
                $success = preg_match($pattern, $cpf, $match);

                if ($success || (is_numeric($cpf) && strlen($cpf) == 11)) {

                    if (!in_array($row['cpf'], $cpfs)) {

                        $username = str_replace(" ", "", $cpf);
                        $username = str_replace(".", "", $username);
                        $username = str_replace("-", "", $username);
                        try {
                            $this->checkEm();
                            $aluno = (new Aluno())
                                ->setName($row['name'])
                                ->setEnabled(true)
                                ->setPlainPassword($username)
                                ->setUsername($username)
                                ->setEmail($username . '@portabilis.com')
                                ->setCpf($cpf)
                                ->setRg($row['rg'])
                                ->setTelefone($row['phone'])
                                ->setDataNascimento(new \DateTime($row['birthday']))
                                ->setIdImported($row['id']);
                            $this->em->persist($aluno);

                            $cpfs[] = $cpf;
                            $currentSize++;
                            if (($currentSize % $batchSize) === 0) {
                                $cpfs = array();
                                $this->em->flush();
                                $this->em->clear();
                            }
                        } catch (UniqueConstraintViolationException $exception) {
                            continue;
                        }
                    }
                }
                $progress->advance();
            }
            try {
                $this->checkEm();
                $this->em->flush();
            } catch (UniqueConstraintViolationException $exception) {}
            unset($cpfs);
            $this->em->clear();
            $progress->finish();
            $io->newLine();
            $io->success('Students imported!');
        } else {
            $io->error("No such a csv file");
        }
    }

    public function checkEm()
    {
        if (!$this->em->isOpen()) {
            $this->em = $this->em->create(
                $this->em->getConnection(), $this->em->getConfiguration());
        }
    }
}