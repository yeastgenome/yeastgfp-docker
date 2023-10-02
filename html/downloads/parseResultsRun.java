import java.util.regex.*;
import java.io.*;
import java.sql.*;
import java.util.*;
import java.text.*;
import javaGFPLibraries.*;
import util.*;
import cern.jet.stat.Probability.*;



public class parseResultsRun {
    /* ----  FLAGS  ---- */
    /* THE HEX OPTS */
    static boolean hexOptsThrown = false;
    static int optBin = 0;
    static int maxOpt = 32;
    static int includeOnlyVisualizedOrfs                      = 0x00001;
    static int orderByOrfName                                 = 0x00002;
    static int includeOrfidInOutFile                          = 0x00004;
    static int includeOrfNumberInOutFile                      = 0x00008;
    static int includeOrfNameInOutFile                        = 0x00010;
    static int includeOrfSubcellTableInOutFile                = 0x00020;
    static int includeOrfSubcellSummaryInOutFile              = 0x00040;
    static int orderByVisualized                              = 0x00080;


	
    boolean gf(int optMask) throws Exception {
	if(!hexOptsThrown) {
	    throw(new Exception());
	}
	return ((optBin & optMask) != 0);
    }
	
    /* INDIVIDUAL FLAGS */

    static String geneFileName = "";
    static String outFileName = "";

	
    /* FLAG-RELATED VARS */
    static BufferedReader geneFile;
    static PrintWriter outFile;
    static PrintWriter rScriptFile;
	
	
    public void run(String[] args) throws Exception {
	/* PARSE OUT THE FLAGS */
	for(int a=0; a<args.length; a++) {
	    String flag = args[a];
		
	    if(flag.compareTo("-opt") == 0) { /* THE HEX OPTION FLAG */
		if((++a)>=args.length) {
		    throw(new Exception("usage"));
		}
		hexOptsThrown = true;
		String optBinStr = args[a];
		this.optBin = Integer.decode(optBinStr).intValue();
	    } else if(flag.compareTo("-geneFile") == 0) { /* GENE FILE */
		if((++a)>=args.length) {
		    throw(new Exception("usage"));
		}
		geneFileName = args[a];
	    } else if(flag.compareTo("-outFile") == 0) { /* OUT FILE */
		if((++a)>=args.length) {
		    throw(new Exception("usage"));
		}
		outFileName = args[a];
	    } else {
		throw(new Exception("usage"));
	    }
	}

	/* SET UP THE OrfInfo CLASS WITH ITS CLASS FIELDS, BASED ON THE FLAGS
	   WE GET HERE.
	*/
	if(gf(includeOrfidInOutFile)) {
	    OrfInfo.printOrfid = true;
	}
	if(gf(includeOrfNumberInOutFile)) {
	    OrfInfo.printOrfnumber = true;
	}
	if(gf(includeOrfNameInOutFile)) {
	    OrfInfo.printOrfname = true;
	}
	if(gf(includeOnlyVisualizedOrfs)) {
	    OrfInfo.dontPrintIfUnvisualized = true;
	}
	if(gf(includeOrfSubcellTableInOutFile)) {
	    OrfInfo.printOrfSubcellTable = true;
	}
	if(gf(includeOrfSubcellSummaryInOutFile)) {
	    OrfInfo.printOrfSummary = true;
	}
	if(gf(orderByOrfName)) {
	    OrfInfo.sortByOrfname = true;
	}
	if(gf(orderByVisualized)) {
	    OrfInfo.sortByVisualized = true;
	}
	
	gfpDB gfp = new gfpDB();
	
	try {
	    geneFile = new BufferedReader(new FileReader(geneFileName));
	} catch(Exception e) {
	    System.out.println("you must enter a gene file name");
	    throw(e);
	}
	    
	File f = new File(outFileName);
	outFile = new PrintWriter(new FileWriter(f));
	
	/* example of getting flag info 
	   if(gf(dumpRScript)) {
	   f = new File(rScriptFileName);
	   rScriptFile = new PrintWriter(new FileWriter(f));
	   }
	*/
	    
	String captureOrfNameOrNumberRegexp = "(\\S+)"; /* WHITESPACE DELIMITED FILE */
	Pattern pCaptureOrfNameOrNumber = Pattern.compile(captureOrfNameOrNumberRegexp);
	    
	String inLine;

	Set orfSet = new HashSet();
	Set unmatchedOrfSet = new HashSet();

	while((inLine = geneFile.readLine()) != null) {
	    Matcher myM = pCaptureOrfNameOrNumber.matcher(inLine);
	    while(myM.find()) {
		try {
		    int orfid = gfp.getOrfid(myM.group(1));
		    orfSet.add(new Integer(orfid));
		} catch(Exception e) {
		    unmatchedOrfSet.add(myM.group(1));
		}
	    }
	}

	if(!unmatchedOrfSet.isEmpty()){

	    outFile.println("the following orfs did not match anything in the database");
		
	    for(Iterator i1 = unmatchedOrfSet.iterator(); i1.hasNext();) {
		String unmatchedOrf = (String)i1.next();
		outFile.println(unmatchedOrf);
	    }
		
	    outFile.println("END UNMATCHED ORFS");
		
	}

	if(!orfSet.isEmpty()) {
	    OrfInfo.init();
	    outFile.println(OrfInfo.sprintHeader());

	    List orfInfoList = new LinkedList();
	    
	    for(Iterator i1 = orfSet.iterator(); i1.hasNext();) {

		int orfId = ((Integer)i1.next()).intValue();
		String orfNumber = gfp.getOrfnumber(orfId);
		String orfName = gfp.getOrfname(orfId);
		
		OrfInfo myOrf = new OrfInfo(orfId,orfNumber,orfName);

		Set ourLocs;
		ourLocs = gfp.getLocalizationsForOrf(orfId);
		for(Iterator i2 = ourLocs.iterator(); i2.hasNext();) {
		    int subcellid = ((Integer)i2.next()).intValue();
		    myOrf.addLocalization(subcellid);
		}

		//		outFile.print(myOrf);
		orfInfoList.add(myOrf);
	    }

	    Collections.sort(orfInfoList);

	    for(Iterator i3=orfInfoList.iterator(); i3.hasNext();) {
		outFile.print((OrfInfo)i3.next());
	    }
	    
	}
	
	outFile.close();
	geneFile.close();
    }
}
