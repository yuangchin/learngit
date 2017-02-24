<?php
defined('WEKIT_VERSION') || exit('Forbidden');

define('EOF_CENTRAL_DIRECTORY', 0x06054b50); //'end of central directory record'����ı��
define('LOCAL_FILE_HEADER', 0x04034b50); //'Local file header'������
define('CENTRAL_DIRECTORY', 0x02014b50); //'Central directory'������

class PwZip {
	
	var $fileHeaderAndData = array();
	var $centralDirectory = array();
	var $localFileHeaderOffset = 0;
	var $fileHandle = '';
	
	/**
	 * ���Ӵ�ѹ�����ļ�
	 * @param $data string ��ѹ�����ַ���
	 * @param $filename string �ļ���
	 * @param $timestamp int ʱ���
	 * @return bool
	 */
	function addFile($data, $filename, $timestamp = 0){
		if (!$this->_checkZlib()) return false;
		
		list($modTime, $modDate) = $this->_getDosFormatTime($timestamp);
		$unCompressedSize = strlen($data);
		$crcValue = crc32($data);
		$compressedData = gzcompress($data);
		$compressedData = substr(substr($compressedData, 0, strlen($compressedData) - 4), 2); // crc problem
		$compressedSize = strlen($compressedData);
		$filenameLength = strlen($filename);
		
		$header    = pack('V', LOCAL_FILE_HEADER); 			// local file header signature
		$header   .= "\x14\x00";           					// version needed to extract
		$header   .= "\x00\x00";            				// general purpose bit flag  
		$header   .= "\x08\x00";           					// compression method, deflated used here
		$header   .= pack('vv', $modTime, $modDate);        // last mod file time, last mod file date
		$header   .= pack('V', $crcValue);       		 	// crc-32
		$header   .= pack('V', $compressedSize);       		// compressed size
		$header   .= pack('V', $unCompressedSize);       	// uncompressed size
		$header   .= pack('v', $filenameLength);       		// file name length
		$header   .= pack('v', 0);       					// extra field length
		$header   .= $filename;      						// filename
		$header   .= $compressedData;      					// file data
		$header   .= $this->_getDataDescriptor($crcValue, $compressedSize, $unCompressedSize); // Data descriptor
		$this->fileHeaderAndData[] = $header;
		//central directory
		$this->centralDirectory[] = $this->_getCentralDirectory($modTime, $modDate, $crcValue, $compressedSize, $unCompressedSize, $filenameLength, strlen($header), $filename);
		return true;
	}
	
	/**
	 * ����ѹ���������
	 * @return string ѹ���������
	 */
	function getCompressedFile(){
		$fileHeaderAndData = implode('', $this->fileHeaderAndData);
		$centralDirectory = implode('', $this->centralDirectory);
		
		$return = $fileHeaderAndData . $centralDirectory;
		$return .= pack('V', EOF_CENTRAL_DIRECTORY); 			// end of central dir signature
		$return .= pack('v', 0); 								// Number of this disk
		$return .= pack('v', 0); 								// Disk where central directory starts
		$return .= pack('v', count($this->centralDirectory)); 	// central directory on this disk
		$return .= pack('v', count($this->centralDirectory)); 	// total number of entries in the central directory
		$return .= pack('V', strlen($centralDirectory));        // size of central dir
		$return .= pack('V', strlen($fileHeaderAndData));       // offset to start of central dir
		$return .= "\x00\x00";                            		// .zip file comment length
		return $return;
	}
	
	/**
	 * ��ѹ��һ���ļ�
	 * @param $file string �ļ���
	 * @return array ��ѹ��������ݣ����а���ʱ�䡢�ļ���������
	 */
	function extract($file) {
		$extractedData = array();
		if (!$file || !is_file($file)) return false;
		$filesize = sprintf('%u', filesize($file));
		
		$this->fileHandle = fopen($file, 'rb');
		$fileData = fread($this->fileHandle, $filesize);
		
		$EofCentralDirData = $this->_findEOFCentralDirectoryRecord($filesize); //��ȡ'End of central directory record'���������
		if (!is_array($EofCentralDirData)) return false;
		$centralDirectoryHeaderOffset = $EofCentralDirData['centraldiroffset'];
		for ($i = 0; $i < $EofCentralDirData['totalentries']; $i++) {
			rewind($this->fileHandle);
			fseek($this->fileHandle, $centralDirectoryHeaderOffset);
			$centralDirectoryData = $this->_readCentralDirectoryData(); // ��ȡ'Central directory' ��������
			$centralDirectoryHeaderOffset += 46 + $centralDirectoryData['filenamelength'] + $centralDirectoryData['extrafieldlength'] + $centralDirectoryData['commentlength'];
			if (!is_array($centralDirectoryData) || substr($centralDirectoryData['filename'], -1) == '/') continue;
			$data = $this->_readLocalFileHeaderAndData($centralDirectoryData); // ��ȡѹ��������
			if (!$data) continue;
			$extractedData[$i] = array(
				'filename' => $centralDirectoryData['filename'],
				'timestamp' => $centralDirectoryData['time'],
				'data' => $data,
			);
		}
		fclose($this->fileHandle);
		return $extractedData;
	}
	
	/**
	 * ��ʼ��
	 */
	function init() {
		$this->fileHeaderAndData = $this->centralDirectory = array();
		$this->localFileHeaderOffset = 0;
		return true;
	}
	
	/**
	 * ȡ��ѹ�������е�'Local file header'�����ѹ��������
	 * @param $centralDirectoryData array 'Central directory' ��������
	 * @return array
	 */
	function _readLocalFileHeaderAndData($centralDirectoryData) {
		fseek($this->fileHandle, $centralDirectoryData['localheaderoffset']);
		$localFileHeaderSignature = unpack('Vsignature', fread($this->fileHandle, 4)); // 'Local file header' ����ı��
		if ($localFileHeaderSignature['signature'] != 0x04034b50) return false;
		$localFileHeaderData = fread($this->fileHandle, 26); // 'Local file header' �����, file name, extra field �������
		$localFileHeaderData = unpack('vextractversion/vflag/vcompressmethod/vmodtime/vmoddate/Vcrc/Vcompressedsize/Vuncompressedsize/vfilenamelength/vextrafieldlength', $localFileHeaderData);
		$localFileHeaderData['filenamelength'] && $localFileHeaderData['filename'] = fread($this->fileHandle, $localFileHeaderData['filenamelength']); //��ȡ�ļ���
		$localFileHeaderData['extrafieldlength'] && $localFileHeaderData['extrafield'] = fread($this->fileHandle, $localFileHeaderData['extrafieldlength']); //��ȡextra field
		if (!$this->_checkLocalFileHeaderAndCentralDir($localFileHeaderData, $centralDirectoryData)) return false;
		
		if ($localFileHeaderData['flag'] & 1) return false; //�ļ����ܹ�
		$compressedData = fread($this->fileHandle, $localFileHeaderData['compressedsize']);
		$data = $this->_unCompressData($compressedData, $localFileHeaderData['compressmethod']);
		
		if (crc32($data) != $localFileHeaderData['crc'] || strlen($data) != $localFileHeaderData['uncompressedsize']) return false; //crc32 У�鲻һ�»򳤶Ȳ�һ��
		return $data;
	}
	
	/**
	 * ��ѹ��ѹ��������
	 * @param $data string ��ѹ��������
	 * @param $compressMethod int ѹ���ķ�ʽ
	 * @return string ��ѹ�������
	 */
	function _unCompressData($data, $compressMethod) { // ���ݾ����ѹ����ʽ��ѹ����Ŀǰ��֧��deflate ѹ����ʽ��deflate, deflate64, bzip2 ��
		if (!$compressMethod) return $data;
		switch ($compressMethod) {
			case 8 : // compressed by deflate
				$data = gzinflate($data);
				break;
			default :
				return false;
				break;
		}
		return $data;
	}
	
	/**
	 * У�� 'Local file header' �� 'Central directory'
	 * @param unknown_type $localFileHeaderData
	 * @param unknown_type $centralDirectoryData
	 * @return bool
	 */
	function _checkLocalFileHeaderAndCentralDir($localFileHeaderData, $centralDirectoryData) { 
		return true; //��ʱ����֤������Ҫʱ����չ
	}
	
	/**
	 * ��ȡ'Central directory' ��������
	 * @return string
	 */
	function _readCentralDirectoryData() {
		$centralDirectorySignature = unpack('Vsignature', fread($this->fileHandle, 4)); // 'Central directory' ����ı��
		if ($centralDirectorySignature['signature'] != 0x02014b50) return false;
		$centralDirectoryData = fread($this->fileHandle, 42); // 'Central directory' ��������, file name, extra field, file comment �������
		$centralDirectoryData = unpack('vmadeversion/vextractversion/vflag/vcompressmethod/vmodtime/vmoddate/Vcrc/Vcompressedsize/Vuncompressedsize/vfilenamelength/vextrafieldlength/vcommentlength/vdiskstart/vinternal/Vexternal/Vlocalheaderoffset', $centralDirectoryData);
		$centralDirectoryData['filenamelength'] && $centralDirectoryData['filename'] = fread($this->fileHandle, $centralDirectoryData['filenamelength']); //��ȡ�ļ���
		$centralDirectoryData['extrafieldlength'] && $centralDirectoryData['extrafield'] = fread($this->fileHandle, $centralDirectoryData['extrafieldlength']); //��ȡextra field
		$centralDirectoryData['commentlength'] && $centralDirectoryData['comment'] = fread($this->fileHandle, $centralDirectoryData['commentlength']); //��ȡ file comment
		$centralDirectoryData['time'] = $this->_recoverFromDosFormatTime($centralDirectoryData['modtime'], $centralDirectoryData['moddate']); //��ȡʱ����Ϣ
		return $centralDirectoryData;
	}
	
	/**
	 * ��ȡ'end of central directory record'��������
	 * @param $filesize int �ļ���С
	 * @return string 
	 */
	function _findEOFCentralDirectoryRecord($filesize) {
		fseek($this->fileHandle, $filesize - 22); // 'End of central directory record' һ����û��ע�͵������λ�ڸ�λ��
		$EofCentralDirSignature = unpack('Vsignature', fread($this->fileHandle, 4));
		if ($EofCentralDirSignature['signature'] != 0x06054b50) { // 'End of central directory record' ����ĩβ22���ֽڵ�λ�ã�����ע�͵����
			$maxLength = 65535 + 22; //'End of central directory record' ���������ܵĳ��ȣ���Ϊ����ע�ͳ��ȵ�����ĳ���Ϊ2�ֽڣ�2���ֽ����ɱ���ĳ�����65535����0xFFFF��22Ϊ'End of central directory record' ��ȥע�ͺ�ĳ���
			$maxLength > $filesize && $maxLength = $filesize; //����ܳ��������ļ��Ĵ�С
			fseek($this->fileHandle, $filesize - $maxLength);
			$searchPos = ftell($this->fileHandle);
			while ($searchPos < $filesize) {
				fseek($this->fileHandle, $searchPos);
				$sigData = unpack('Vsignature', fread($this->fileHandle, 4));
				if ($sigData['signature'] == 0x06054b50) {
					break;
				}
				$searchPos++;
			}
		}
		$EofCentralDirData = unpack('vdisknum/vdiskstart/vcentraldirnum/vtotalentries/Vcentraldirsize/Vcentraldiroffset/vcommentlength', fread($this->fileHandle, 18)); // 'End of central directory record'�����signature��ע���������
		$EofCentralDirData['commentlength'] && $EofCentralDirData['comment'] = fread($this->fileHandle, $EofCentralDirData['commentlength']);
		return $EofCentralDirData;
	}
	
	/**
	 * ���PHP zlib��չ��û������
	 * @return bool
	 */
	function _checkZlib() {
		return (extension_loaded('zlib') && function_exists('gzcompress')) ? true : false;
	}
	
	/**
	 * ��װ 'Central directory' ��������
	 * @param $modTime
	 * @param $modDate
	 * @param $crc
	 * @param $compressedSize
	 * @param $unCompressedSize
	 * @param $filenameLength
	 * @param $fileHeaderLength
	 * @param $filename
	 * @return string
	 */
	function _getCentralDirectory($modTime, $modDate, $crc, $compressedSize, $unCompressedSize, $filenameLength, $fileHeaderLength, $filename) {
		$centralDirectory = pack('V', CENTRAL_DIRECTORY);			// central file header signature
		$centralDirectory .= "\x00\x00";							// version made by
		$centralDirectory .= "\x14\x00";							// version needed to extract
		$centralDirectory .= "\x00\x00";							// general purpose bit flag
		$centralDirectory .= "\x08\x00";							// compression method
		$centralDirectory .= pack('vv', $modTime, $modDate);		// last mod file time, last mod file date
		$centralDirectory .= pack('V', $crc);						// crc-32
		$centralDirectory .= pack('V', $compressedSize);			// compressed size
		$centralDirectory .= pack('V', $unCompressedSize);			// uncompressed size
		$centralDirectory .= pack('v', $filenameLength);			// file name length
		$centralDirectory .= pack('v', 0 );            				// extra field length
		$centralDirectory .= pack('v', 0 );             			// file comment length
		$centralDirectory .= pack('v', 0 );             			// disk number start
		$centralDirectory .= pack('v', 0 );             			// internal file attributes
		$centralDirectory .= pack('V', 32 );            			// external file attributes - 'archive' bit set
		$centralDirectory .= pack('V', $this->localFileHeaderOffset); 		// relative offset of local header
		$this->localFileHeaderOffset += $fileHeaderLength;
		$centralDirectory .= $filename;								// file name
		return $centralDirectory;
	}
	
	/**
	 * ��װ 'Data descriptor' ��������
	 * @param $crc
	 * @param $compressedSize
	 * @param $unCompressedSize
	 * @return string
	 */
	function _getDataDescriptor($crc, $compressedSize, $unCompressedSize) {
		return '';	// return string only when bit 3 of the general purpose bit flag is set
		//return pack('VVV', $crc, $compressedSize, $unCompressedSize);
	}
	
	/**
	 * ��ʽ��ʱ��ΪDOS��ʽ
	 * @param $timestamp
	 * @return array
	 */
	function _getDosFormatTime($timestamp = 0) {
		$timestamp = (int) $timestamp;
		$time = $timestamp === 0 ? getdate() : getdate($timestamp);
		if ($time['year'] < 1980) {
            $time['year']    = 1980;
            $time['mon']     = 1;
            $time['mday']    = 1;
            $time['hours']   = 0;
            $time['minutes'] = 0;
            $time['seconds'] = 0;
        }
		$modTime = ($time['hours'] << 11) + ($time['minutes'] << 5) + $time['seconds'] / 2;
		$modDate = (($time['year'] - 1980) << 9) + ($time['mon'] << 5) + $time['mday'];
		return array($modTime, $modDate);
	}
	
	/**
	 * ��ԭDOS��ʽ��ʱ��Ϊʱ���
	 * @param $time
	 * @param $date
	 * @return int
	 */
	function _recoverFromDosFormatTime($time, $date) {
		$year = (($date & 0xFE00) >> 9) + 1980;
		$month = ($date & 0x01E0) >> 5;
		$day = $date & 0x001F;
		$hour = ($time & 0xF800) >> 11;
		$minutes = ($time & 0x07E0) >> 5;
		$seconds = ($time & 0x001F)*2;
		return mktime($hour, $minutes, $seconds, $month, $day, $year);
	}
}
?>