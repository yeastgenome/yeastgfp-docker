import java.util.*;
import javaGFPLibraries.*;

public class OrfInfo implements Comparable {
    
    int orfId;
    String orfNumber;
    String orfName;
    List locs;
    boolean visualized;
    String tapStrainId;

    /* class fields */
    public static boolean printOrfid = false;
    public static boolean printOrfnumber = false;
    public static boolean printOrfname = false;
    public static boolean printOrfSummary = false;
    public static boolean printOrfSubcellTable = false;
    public static boolean printVisible = true;
    public static boolean printTagged = true;
    public static boolean dontPrintIfUnvisualized = false;
    public static boolean sortByVisualized = false;
    public static boolean sortByOrfnumber = false;
    public static boolean sortByOrfname = false;
    public static boolean printTapTagged = false;
    public static boolean printAbundance = false;
    public static boolean printError = false;
    public static boolean printOligoSeq = false;
    public static boolean printCheckOligoSeq = false;
    
    public static gfpDB gfp;
    
    public OrfInfo(int orfId, String orfNumber, String orfName) throws Exception {

	this.orfId = orfId;
	this.orfNumber = orfNumber;
	this.orfName = orfName;
	
	this.locs = new LinkedList();

	if (gfp == null) {
	    gfp = new gfpDB();
	}

	this.visualized = gfp.wasOrfVisualized(orfId);
	this.tapStrainId = gfp.getTapStrainId(orfId);	

    }
    
    public void addLocalization(int beerkeg) {
	
	Integer locId = new Integer(beerkeg);
	this.locs.add(locId);

	Collections.sort(this.locs);
	
    }

    public static void init() throws Exception {
	if (gfp == null) {
	    gfp = new gfpDB();
	}
    }
    
    public static String sprintHeader() throws Exception {
	String retString = "";
	
	if(OrfInfo.printOrfid) {
	    retString += "orfid\t";
	}
	if(OrfInfo.printOrfnumber) {
	    retString += "yORF\t";
	}
	if(OrfInfo.printOrfname) {
	    retString += "gene name\t";
	}

	/* GFP TAGGED */
	if(OrfInfo.printTagged) {
	    retString += "GFP tagged?\t";
	}

	/* VISUALIZED */
	if(OrfInfo.printVisible) {
	    retString += "GFP visualized?\t";
	}

	/* TAP TAGGED */
	if(OrfInfo.printTapTagged) {
	    retString += "TAP visualized?\t";
	}

	/* ABUNDANCE */
	if(OrfInfo.printAbundance) {
	    retString += "abundance\t";	    
	}

	/* ERROR */
	if(OrfInfo.printError) {
	    retString += "error\t";	    
	}

	/* OLIGO SEQ */
	if(OrfInfo.printOligoSeq) {
	    retString += "F2 Seq\tR1 Seq\t";	    
	}

	/* CHECK OLIGO SEQ */
	if(OrfInfo.printCheckOligoSeq) {
	    retString += "Check Seq\t";	    
	}
	    
	/* SUMMARY FIELD */
	if(OrfInfo.printOrfSummary) {
	    retString += "localization summary\t";
	}	

	/* COLUMN SUBCELL FIELDS */
	if(printOrfSubcellTable) {
	    List subcells = gfp.getOrderedSubcellList();
	    for(Iterator i=subcells.iterator(); i.hasNext();) {
		Integer thisSubcell = (Integer)i.next();
		retString += gfp.getSubcellName(thisSubcell.intValue()) + "\t";
	    }
	}

	return retString;
    }

  

    public String toString() {
	/* overriding Object method */
	/* allows us to print this how we want it, not the memory address */

	String retString = "";
	
	try {
	    if(dontPrintIfUnvisualized && !gfp.wasOrfVisualized(orfId)) {
		return "";
	    }
	} catch (Exception e) {
	    retString += "SOMETHING BAD HAPPENED";
	}

	
	if(OrfInfo.printOrfid) {
	    retString += this.orfId + "\t";
	}
	if(OrfInfo.printOrfnumber) {
	    retString += this.orfNumber + "\t";
	}
	if(OrfInfo.printOrfname) {
	    retString += this.orfName + "\t";
	}

	/* GFP TAGGED */
	try {
	    if(OrfInfo.printTagged && gfp.wasOrfConfirmedTagged(orfId)) {
		retString += "tagged\t";
	    } else {
		retString += "not tagged\t";
	    }
	} catch (Exception e) {
	    System.out.println(e);
	}

	/* VISUALIZED */
	try {
	    if(OrfInfo.printVisible && this.visualized) {
		retString += "visualized\t";
	    } else {
		retString += "not visualized\t";
	    }
	} catch (Exception e) {
	    System.out.println(e);
	}

	/* TAP TAGGED? */
	if(OrfInfo.printTapTagged) {

	    try {
		
		String tapTagged = gfp.getTapVisualized(tapStrainId);
		retString += tapTagged;

	    } catch (Exception e) {
		System.out.println("you can't write java 1");
	    }

	}


	/* ABUNDANCE */
	if(OrfInfo.printAbundance) {

	    try {

		String abundance = gfp.getAbundanceForOrf(tapStrainId);
		
		retString += abundance;

	    } catch (Exception e) {
		System.out.println("you can't write java 2 ");
	    }

	}


	/* ERROR */
	if(OrfInfo.printError) {

	    try {

		double error = gfp.getErrorForOrf(tapStrainId);

		if (error == 0) {
		    retString += "NA\t";	    	    
		} else {
		    retString += error;
		    retString += "\t";	    	    
		}		    

	    } catch (Exception e) {
		System.out.println("you can't write java 3 ");
		System.out.println(tapStrainId);
		System.out.println(e);
		System.exit(666);
	    }
	 
	}

	if(OrfInfo.printOligoSeq) {

	    try {

		String oligoSeq = gfp.getOligoSeqForOrf(orfId);

		retString += oligoSeq;
		
	    } catch (Exception e) {
		System.out.println("text output surpasses your java ability");
		System.out.println(orfId);
		System.out.println(e);
		System.exit(666);
	    }
	}

	if(OrfInfo.printCheckOligoSeq) {

	    try {
		
		String checkOligoSeq = gfp.getCheckOligoSeqForOrf(orfId);

		retString += checkOligoSeq;
		
	    } catch (Exception e) {
		System.out.println("text output surpasses your java ability 2");
		System.out.println(orfId);
		System.out.println(e);
		System.exit(666);
	    }
	}
	
	
	/* SUMMARY FIELD */
	if(OrfInfo.printOrfSummary) {
	    boolean firstTime = true;
	    for(Iterator i1 = this.locs.iterator(); i1.hasNext();) {
		try {
		    int subcellid = ((Integer)i1.next()).intValue();
		    String subcellname = gfp.getSubcellName(subcellid);
		    if(!firstTime) retString += ",";
		    retString += subcellname;
		    firstTime = false;

		} catch (Exception e) {
		    System.out.println(e);
		}
	    }
	    retString += "\t";
	}	

	/* COLUMN SUBCELL FIELDS */
	if(printOrfSubcellTable) {
	    try {
		List subcells = gfp.getOrderedSubcellList();
		for(Iterator i=subcells.iterator(); i.hasNext();) {
		    Integer thisSubcell = (Integer)i.next();
		    if(this.locs.contains(thisSubcell)) {
		        retString += "T\t";//gfp.getSubcellName(thisSubcell.intValue());
		    } else {
			retString += "F\t";
		    }
		}
	    } catch (Exception e) {
		System.out.println("ouch");
	    }
	    retString += "\t";
	}
	
	return retString + "\n";
    }


    public int compareTo(Object o) {
	OrfInfo second = (OrfInfo)o;
	
	if(OrfInfo.sortByVisualized) {
	    if(this.visualized && !second.visualized) {
		return -1;
	    } else if(!this.visualized && second.visualized) {
		return 1;
	    }
	}

	if(OrfInfo.sortByOrfnumber) {
	    return(orfNumber.compareTo(second.orfNumber));
	}
	
	if(OrfInfo.sortByOrfname) {
	    return(orfName.compareTo(second.orfName));
	}
	
	return 0;
	//	if(OrfInfo.sortByVisualized)

    }


    
}
