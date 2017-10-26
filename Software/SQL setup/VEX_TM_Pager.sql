-- phpMyAdmin SQL Dump
-- version 4.2.12deb2+deb8u2
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Sep 29, 2017 at 10:55 AM
-- Server version: 5.5.57-0+deb8u1
-- PHP Version: 5.6.30-0+deb8u1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `VEX_TM`
--
CREATE DATABASE IF NOT EXISTS `VEX_TM` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci;
USE `VEX_TM`;

-- --------------------------------------------------------

--
-- Table structure for table `Matches`
--

CREATE TABLE IF NOT EXISTS `Matches` (
  `seq` int(11) NOT NULL,
  `Division` varchar(10) NOT NULL,
  `MatchNumber` varchar(10) NOT NULL,
  `Red1` varchar(10) NOT NULL,
  `Red2` varchar(10) NOT NULL,
  `Red3` varchar(10) NOT NULL,
  `Blue1` varchar(10) NOT NULL,
  `Blue2` varchar(10) NOT NULL,
  `Blue3` varchar(10) NOT NULL,
  `UID` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `Teams`
--

CREATE TABLE IF NOT EXISTS `Teams` (
  `seq` int(11) NOT NULL,
  `Division` varchar(10) NOT NULL,
  `TeamNumber` varchar(10) NOT NULL,
  `UID` varchar(20) NOT NULL,
  `TeamPagerBank` varchar(20) NOT NULL,
  `TeamPager` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `ToPage`
--

CREATE TABLE IF NOT EXISTS `ToPage` (
`seq` int(11) NOT NULL,
  `division` varchar(10) NOT NULL,
  `type` varchar(1) NOT NULL,
  `id` varchar(8) NOT NULL,
  `done` varchar(1) NOT NULL,
  `tmp` varchar(10) NOT NULL,
  `UID` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `Matches`
--
ALTER TABLE `Matches`
 ADD PRIMARY KEY (`seq`);

--
-- Indexes for table `Teams`
--
ALTER TABLE `Teams`
 ADD PRIMARY KEY (`seq`), ADD KEY `seq` (`seq`);

--
-- Indexes for table `ToPage`
--
ALTER TABLE `ToPage`
 ADD PRIMARY KEY (`seq`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `ToPage`
--
ALTER TABLE `ToPage`
MODIFY `seq` int(11) NOT NULL AUTO_INCREMENT;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
