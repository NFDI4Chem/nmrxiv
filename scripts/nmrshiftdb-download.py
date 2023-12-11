import os
import sys
import requests
from rdkit.Chem import SDMolSupplier
from helpers import*


def main():
    url = "https://sourceforge.net/projects/nmrshiftdb2/files/data/nmrshiftdb2rawdata.nmredata.sd/download"
    suppl = get_sdf_as_SDMolSupplier(url, "nmrshiftdb2.nmredata.sd")
    print('The total number of NMReData entries found in NMRShiftDB with raw data, including duplicated molecules, is: '+ str(len(suppl)) + '\n')
    
    lst = download_zips(suppl)
    
    print('NMRShiftDB downloading is finished\n')
    print('The number of unique authors combinations corresponding to nmrXiv projects is: ' + str(lst[0]))
    print('The number of molecules corresponding to nmrXiv samples is: ' + str(lst[1]))
    print('The number of downloaded spectra corresponding to nmrXiv spectra is: ' + str(lst[2]))
    print('The number of molecules with both 1D and 2D spectra is: ' + str(lst[3]))


       
    unzipper()
    rename_folders()
    structure_folders()
    delete_empty_folders(os.getcwd())
    print('\nThe process has ended successfully')
if __name__ == "__main__":
    sys.exit(main())
